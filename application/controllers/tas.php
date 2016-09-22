<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 9:30 AM
 */

class Tas extends Admin_Controller{

    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('tas_m');
        $this->load->model('towns_m');
        $this->load->model('regions_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Technical Assistants";
        $this->load->view('templates/header', $data);
        $this->load->view('technical/view_techs',$data);

    }

    function datatables(){
        $this->datatables->select('area_tas.id AS id,area_tas.name as name,mobile,email,division, area_tas.modified As modified,area_tas.created As created')
            ->unset_column('id')
            ->add_column('actions', get_tas_buttons('$1'), 'id')
            ->from('area_tas');
        echo $this->datatables->generate();
    }



    function add(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('name', "TA's Name", 'required|max_length[60]|is_unique[area_tas.name]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|exact_length[12]|is_unique[area_tas.mobile]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]|is_unique[area_tas.email]');
        $this->form_validation->set_rules('division', 'TA Division', 'required|max_length[40]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $name="";
        $mobile ="";
        $email="";
        $division ="";

        // has the form been submitted
        if($this->input->post()){
            $name = trim($this->input->post('name'));
            $mobile = ($this->input->post('mobile'));
            $email = $this->input->post('email');
            $division = ($this->input->post('division'));


            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //Save new distributor
                $saved = $this->tas_m->save_tas(ucwords($name),$mobile,strtolower($email),strtoupper($division));

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Technical Assistant added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('tas/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'Technical Assistant NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('tas/add');
                }
            }
        }


        $data['name']=$name;
        $data['mobile']=$mobile;
        $data['division']=$division;
        $data['email']=$email;

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Stockist";
        $this->load->view('templates/header', $data);
        $this->load->view('technical/add_tech',$data);

    }

    function edit($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->tas_m->get_tas($id);

            if($to_edit){
                //display reply view
                $data['id']=$id;
                $data['name']=$to_edit->name;
                $data['mobile']=$to_edit->mobile;
                $data['email']=$to_edit->email;
                $data['division']=$to_edit->division;


            }else{
                //return to tas. no id specified
                $this->session->set_flashdata('appmsg', 'Error encountered! No record found');
                $this->session->set_flashdata('alert_type', 'alert-warning');
                redirect('tas');
            }

        }else{
            //return to tas. no id specified
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('tas');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Technical Assistant";
        $this->load->view('templates/header', $data);
        $this->load->view('technical/edit_tech',$data);

    }

    function modify(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('name', "TA's Name", 'required|max_length[60]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('division', 'Division', 'required|max_length[40]');
        /*$this->form_validation->set_rules('region_id', 'region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');*/

        // has the form been submitted
        if($this->input->post()){
            $id = $this->input->post('id');
            $name = trim($this->input->post('name'));
            $mobile = ($this->input->post('mobile'));
            $email = $this->input->post('email');
            $division = ($this->input->post('division'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                /*//verify name if it exists other than modified field
                $name_exists = $this->tas_m->verify_tas_name($id,$name);

                if($name_exists){
                    //return fail. TAS name already in use
                    $this->session->set_flashdata('appmsg', 'This Technical Assistant  Name "'.$name.'" is already in use by a different TA');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('tas/edit/'.$id);
                }else{*/
                    //Verify mobile number1
                $valid_mobile = $this->__checkMobile($mobile);

                    if(!$valid_mobile){
                        //return fail. TAS mobile already in use
                        $this->session->set_flashdata('appmsg', 'Invalid Mobile Number format: "'.$mobile.'". Use this format: "2547xxxxxxxx"  e.g. 254712345678');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('tas/edit/'.$id);
                    }else{
                        //Verify Email
                        $email_exists = $this->tas_m->verify_tas_email($id,$email);
                        if($email_exists){
                            //return fail. TAS email already in use
                            $this->session->set_flashdata('appmsg', 'This Technical Assistant Email "'.$email.'" is already in use by a different Technical Assistant');
                            $this->session->set_flashdata('alert_type', 'alert-danger');
                            redirect('tas/edit/'.$id);
                        }else{
                            //update TAS
                            $saved = $this->tas_m->update_tas($id,ucwords($name),$mobile,strtolower($email),strtoupper($division));

                            if($saved){
                                // Display success message
                                $this->session->set_flashdata('appmsg', 'Technical Assistant modified successfully!');
                                $this->session->set_flashdata('alert_type', 'alert-success');
                                redirect('tas');

                            }else{
                                // Display fail message
                                $this->session->set_flashdata('appmsg', 'Technical Assistant NOT modified! Check logs');
                                $this->session->set_flashdata('alert_type', 'alert-danger');
                                redirect('tas');
                            }
                        }




                    }

                //}

            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('tas/edit/'.$id);
        }

        redirect('tas');
    }

    function __get_towns($region){
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this->towns_m->get_towns_by_region($region)));
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Technical Assistants";
        $this->load->view('templates/header', $data);
        $this->load->view('technical/import_techs',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/tas/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['overwrite'] = TRUE;
        $config['max_size']	= '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $error = array('error' => $this->upload->display_errors());

            log_message('error','Error: File not imported. '.$error);
            // Display fail message
            $this->session->set_flashdata('appmsg', $error['error']);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('tas/import');
        }else{
            $data = array('upload_data' => $this->upload->data());

            foreach($data['upload_data'] as $item => $value){
                log_message('info','item: '.$item. ' value: '.$value);
            }

            $data2 =  $this->upload->data();
            $file_name= $data2['file_name'];

            $result = $this->import_excel($file_name);

            $importedNo =$result['count'];
            $existing_tas = $result['existing'];
            $notImported = $result['notadded'];

            // Display success message
            $this->session->set_flashdata('existing', $existing_tas);
            $this->session->set_flashdata('notimported', $notImported);
            $this->session->set_flashdata('appmsg', 'Technical Assistants imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('tas/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        $this->load->model('regions_m');
        $this->load->model('towns_m');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/tas/'.$fileName;

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            return array('count'=>'0. Error reading uploaded file','existing'=>'','notadded'=>'');
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'F';

        $addCounter = 0;
        $notAdded = '';
        $notAddedTown = '';
        $notAddedRegion = '';
        $existingTasNames = '';
        $existingMobiles = '';
        $invalidMobiles = '';
        $invalidEmails = '';
        $existingEmails = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $tasData = $rowData[0];

            $tasName = $tasData[0];
            $mobile = trim($tasData[1]);
            $email = trim($tasData[2]);
            $division = trim($tasData[3]);
            $region = trim($tasData[4]);
            $town = trim($tasData[5]);




            if ( $tasName!=null AND $mobile!=null AND $email!=null AND $division!=null AND  $region!=null AND $town!=null ) {
                log_message('info', 'TA Name: ' . $tasName .'. Mobile: ' . $mobile. '. Email: ' . $email.'. Division: ' . $division. '. Region: ' . $region. '. Town: ' . $town);

                // Check region if it exists
                $existsRegion = $this->regions_m->get_region_by_name($region);
               // log_message('info', 'Region: '.print_r($existsRegion));
                if($existsRegion){
                    // Check if Town exists in the region
                    $existsTown = $this->towns_m->check_town_region($town, $existsRegion->id);

                    if($existsTown){

                        // Check tas email exists or not
                        $existsEmail = $this->tas_m->check_tas_email(strtolower($email));

                        if($existsEmail){
                            //Get id of Tech Assistant
                            $tech = $this->tas_m->get_tas_by_email(strtolower($email));

                            //check if Tas exists in town
                            $ta_exists = $this->tas_m->check_tas_in_town($tech->id,$existsTown->id);

                            if($ta_exists){
                                $existingEmails =$existingEmails.' | '.$email.' - '.$existsTown->name;
                                //log as not added
                                log_message('info',$email.' EXISTS! IGNORED ENTRY: '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town);
                            }else{
                                //save stockist
                                $tas_assigned = $this->tas_m->add_tas_to_town($tech->id,$existsTown->id,$existsRegion->id);

                                if($tas_assigned){
                                    $addCounter++;
                                    //log the ta added
                                    log_message('info',$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town.' ADDED SUCCESSFULLY! ');

                                }else{
                                    $notAdded = $notAdded. ' | '.$email.' - '.$tasName;
                                    //log the ta as not added
                                    log_message('info','TAS NOT ASSIGNED! '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town);
                                }
                            }

                        }else{
                            // check email validity
                            if(!valid_email($email)){
                                //Email invalid
                                $invalidEmails = $invalidEmails.' | '.$email;
                                //log as not added
                                log_message('info',$email.' INVALID EMAIL! IGNORED ENTRY: '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town);
                            }else{

                                //check mobile validity
                                $valid_mobile = $this->__checkMobile($mobile);

                                if(!$valid_mobile){
                                    //Mobile invalid
                                    $invalidMobiles =$invalidMobiles.' | '.$mobile;
                                    //log as not added
                                    log_message('info',$mobile.' INVALID MOBILE! IGNORED ENTRY: '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town);
                                }else{

                                    //save stockist
                                    $tas_added = $this->tas_m->add_tas(ucwords($tasName),$mobile,strtolower($email),strtoupper($division),$existsRegion->id,$existsTown->id);

                                    if($tas_added){
                                        $addCounter++;
                                        //log the region added
                                        log_message('info',$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town.' ADDED SUCCESSFULLY! ');

                                    }else{
                                        $notAdded = $notAdded. ' | '.$email.' - '.$tasName;
                                        //log the region as not added
                                        log_message('info','TAS NOT ADDED! '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town);
                                    }

                                }

                            }
                        }


                    }else{
                        $notAddedTown =$notAddedTown. ' | '.$email.' - '.$town;
                        //log the town doesnt exist
                        log_message('info','TECH ASSISTANT NOT ADDED! '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town.' TOWN DOESNT EXIST ');
                    }
                }else{
                    $notAddedRegion =$notAddedRegion. ' | '.$email.' - '.$region;
                    //log the region doesnt exist
                    log_message('info','TECH ASSISTANT NOT ADDED! '.$tasName.' '.$mobile.' '.$email.' '.$division.' '.$region.' '.$town.' REGION DOESNT EXIST ');
                }



            }
        }
        $existingVariables ="";
        if(!empty($existingTasNames)){
            $existingVariables =$existingVariables .'Tech Assistants: ('.$existingTasNames.') ';
        }

        if(!empty($existingMobiles)){
            $existingVariables =$existingVariables .'Mobile Numbers: ('.$existingMobiles.') ';
        }

        if(!empty($existingEmails)){
            $existingVariables =$existingVariables .'Emails Linked to towns: ('.$existingEmails.') ';
        }

        $notAddedVariables ="";
        if(!empty($notAddedRegion)){
            $notAddedVariables = 'NON EXISTING REGIONS: ('.$notAddedRegion.') ';
        }

        if(!empty($notAddedTown)){
            $notAddedVariables =$notAddedVariables .'NON EXISTING TOWNS: ('.$notAddedTown.') ';
        }


        $import_result = array('count'=>$addCounter,'existing'=>$existingVariables,'notadded'=>$notAddedVariables);

        return $import_result;
    }

    function __checkMobile($mobile){
        if(strlen($mobile)===12 ){
            if(substr($mobile,0,3)==254){
                if(ctype_digit($mobile)){
                    return true;
                }else{
                    return false;
                }

            }else{
                return false;
            }

        }else{
            return false;
        }
    }


}