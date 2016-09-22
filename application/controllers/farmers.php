<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 9:30 AM
 */

class Farmers extends Admin_Controller{

    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('farmers_m');
        $this->load->model('towns_m');
        $this->load->model('regions_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Farmers";
        $this->load->view('templates/header', $data);
        $this->load->view('farmers/view_farmers',$data);

    }

    function datatable_active(){
        $this->datatables->select('customers.id AS id,mobile1,customers.name AS name,email,towns.name AS town,regions.name AS region,customers.modified,customers.created')
            ->unset_column('id')
            ->add_column('actions', get_active_farmers_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','ACTIVE')
            ->where('customer_type','FARMER')
            ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('towns', 'customers.town_id = towns.id')
            ->join('regions', 'customers.region_id = regions.id');
        echo $this->datatables->generate();
    }

    function datatable_suspended(){
        $this->datatables->select('customers.id AS id,mobile1,customers.name AS name,email,towns.name AS town,regions.name AS region,customers.modified,customers.created')
            ->unset_column('id')
            ->add_column('actions', get_inactive_farmers_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','SUSPENDED')
            ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('towns', 'customers.town_id = towns.id')
            ->join('regions', 'customers.region_id = regions.id');
        echo $this->datatables->generate();
    }

    function add(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('name', 'Farmer Name', 'required|max_length[100]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]|is_unique[contacts_info.mobile1]|is_unique[contacts_info.mobile2]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[150]');
        $this->form_validation->set_rules('region_id', 'region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $farmer="";
        $mobile ="";
        $email="";
        $town_id ="";
        $region_id ="";

        // has the form been submitted
        if($this->input->post()){
            $farmer = trim($this->input->post('name'));
            $mobile = ($this->input->post('mobile'));
            $email = $this->input->post('email');
            $region_id = ($this->input->post('region_id'));
            $town_id = ($this->input->post('town_id'));


            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //Save new distributor
                $saved = $this->farmers_m->add_farmer(ucwords($farmer),$mobile,strtolower($email),$town_id,$region_id);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Farmer added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('farmers/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Farmer NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('farmers/add');
                }
            }
        }

        //Retrieve regions
        $regions = $this->regions_m->get_all_regions();
        $data['regions'] = $regions;

        //Retrieve towns
        $towns = $this->towns_m->get_all_towns();
        $data['towns'] = $towns;


        $data['name']=$farmer;
        $data['mobile']=$mobile;
        $data['email']=$email;
        $data['region_id']=$region_id;
        $data['town_id']=$town_id;


        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Farmer";
        $this->load->view('templates/header', $data);
        $this->load->view('farmers/add_farmer',$data);

    }

    function edit($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->farmers_m->get_farmer($id);

            //display reply view
            $data['id']=$id;
            $data['name']=$to_edit->name;
            $data['mobile']=$to_edit->mobile1;
            $data['email']=$to_edit->email;
            $data['region_id']=$to_edit->region_id;
            $data['town_id']=$to_edit->town_id;

            //Retrieve regions
            $regions = $this->regions_m->get_all_regions();
            $data['regions'] = $regions;
        }else{
            //return to farmer. no id specified
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('farmers');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Farmer";
        $this->load->view('templates/header', $data);
        $this->load->view('farmers/edit_farmer',$data);

    }

    function modify(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('name', 'Farmer Name', 'required|max_length[100]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[150]');
        $this->form_validation->set_rules('region_id', 'region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');

        // has the form been submitted
        if($this->input->post()){
            $id = $this->input->post('id');
            $name = trim($this->input->post('name'));
            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $region_id = $this->input->post('region_id');
            $town_id = $this->input->post('town_id');

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){
                $farmer_details = $this->farmers_m->get_farmer($id);
                //Verify mobile number1
                $mobile_exists = $this->farmers_m->verify_farmer_mobile($farmer_details->contact_id,$mobile);
                if($mobile_exists){
                    //return fail. mobile already in use
                    $this->session->set_flashdata('appmsg', 'This Mobile Number "'.$mobile.'" is already in use');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('farmers/edit/'.$id);
                }else{
                    //update record
                    $saved = $this->farmers_m->update_farmer($id,ucwords($name),$farmer_details->contact_id,$mobile,strtolower($email),$town_id,$region_id);
                    if($saved){
                        // Display success message
                        $this->session->set_flashdata('appmsg', 'Farmer modified successfully!');
                        $this->session->set_flashdata('alert_type', 'alert-success');
                        redirect('farmers');
                    }else{
                        // Display fail message
                        $this->session->set_flashdata('appmsg', 'Farmer NOT modified! Check logs');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('farmers');
                    }

                }
            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('farmers/edit/'.$id);
        }

        redirect('farmers');
    }

    function sms($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_sms = $this->farmers_m->get_farmer($id);

            //display reply view
            $data['id']=$id;
            $data['name']=$to_sms->name;
            $data['mobile']=$to_sms->mobile1;
            $data['message'] ="";

        }else{
            //return to farmer. No id specified
            $this->session->set_flashdata('appmsg', 'Error encountered! No SMS identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('farmers');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "SMS Farmer";
        $this->load->view('templates/header', $data);
        $this->load->view('farmers/sms_farmer',$data);
    }

    function sendsms(){
        $this->load->library('sms/SmsSender.php');
        $this->load->model('sendsms_model');
        $this->load->model('sms_model');


        // SET VALIDATION RULES
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|exact_length[12]|callback_msisdn_check');
        $this->form_validation->set_rules('message', 'Message', 'required|max_length[160]');

        $msisdn="";
        $message="";

        // has the form been submitted
        if($this->input->post()){
            $id = $this->input->post('id');
            $recipient = $this->input->post('name');
            $msisdn = $this->input->post('mobile');
            $message = $this->input->post('message');

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){
                //Send message
                $recipients= array('tel:'.$msisdn);
                $msg_sent= $this->sendsms_model->send_sms($recipients,$message);

                log_message("info","Sending status: ".$msg_sent);

                if($msg_sent=='success'){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'Message to '.$recipient.'('.$msisdn.') sent successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');

                    //save sms
                    $userid = $this->session->userdata('id');
                    $this->sms_model->save_sms($msisdn,$recipient,$message,$userid);

                    redirect('farmers');
                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'Message to '.$recipient.'('.$msisdn.') failed.');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('farmers');
                }


            }else{
                $errors = validation_errors();
                $this->session->set_flashdata('appmsg', $errors);
                $this->session->set_flashdata('alert_type', 'alert-danger');
                redirect('farmers/sms/'.$id);
            }
        }

        redirect('farmers');
    }

    function activate($id){

        $activated = $this->farmers_m->activate($id);
        if($activated){
            $this->session->set_flashdata('appmsg', 'Farmer successfully activated!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Farmer activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("farmers");
    }

    function suspend($id){

        $deactivated = $this->farmers_m->suspend($id);
        if($deactivated){
            $this->session->set_flashdata('appmsg', 'Farmer successfully suspended!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Farmer activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("farmers");
    }

    function get_towns($region){
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this->towns_m->get_towns_by_region($region)));
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Farmers";
        $this->load->view('templates/header', $data);
        $this->load->view('farmers/import_farmers',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/farmers/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['encrypt_name']  = true;
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
            redirect('farmers/import');
        }else{
            $data = array('upload_data' => $this->upload->data());

            foreach($data['upload_data'] as $item => $value){
                log_message('info','item: '.$item. ' value: '.$value);
            }

            $data2 =  $this->upload->data();
            $file_name= $data2['file_name'];

            $result = $this->import_excel($file_name);

            $importedNo =$result['count'];
            $existing_distributors = $result['existing'];
            $notImported = $result['notadded'];
            $invalid = $result['invalid'];

            // Display success message
            $this->session->set_flashdata('existing', $existing_distributors);
            $this->session->set_flashdata('notimported', $notImported);
            $this->session->set_flashdata('invalid', $invalid);
            $this->session->set_flashdata('appmsg', 'Farmers imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('farmers/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        $this->load->model('regions_m');
        $this->load->model('towns_m');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/farmers/'.$fileName;

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
        $highestColumn = 'G';

        $addCounter = 0;
        $notAdded = '';
        $notAddedTown = '';
        $notAddedRegion = '';
        $existingBizNames = '';
        $existingMobiles = '';
        $invalidMobiles = '';
        $invalidEmails = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $farmerData = $rowData[0];
            $farmerFName = $farmerData[0];
            $farmerMName = $farmerData[1];
            $farmerLName = $farmerData[2];
            $mobile = trim($farmerData[3]);
            $email = trim($farmerData[4]);
            $town = trim($farmerData[5]);
            $region = trim($farmerData[6]);


            $farmerName =trim($farmerFName.' '.$farmerMName.' '.$farmerLName);

            if (!empty($farmerName)AND !empty($mobile) AND !empty($region) AND !empty($town) ) {

                // Check region if it exists
                $existsRegion = $this->regions_m->get_region_by_name($region);
                log_message('info', 'Region: '.print_r($existsRegion));
                if($existsRegion){
                    // Check if Town exists in the region
                    $existsTown = $this->towns_m->check_town_region($town, $existsRegion->id);

                    if($existsTown){

                        // Check Mobile1 name exists or not
                        $existsMobile1 = $this->farmers_m->check_mobile($mobile);

                        if($existsMobile1){
                            //Mobile1 exists
                            $existingMobiles =$existingMobiles.' | '.$mobile;
                            //log as not added
                            log_message('info',$mobile.' EXISTS! IGNORED ENTRY: '.$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town);
                        }else {
                            //check mobile validity
                            $valid_mobile = $this->__checkMobile($mobile);

                            if(!$valid_mobile){
                                //Mobile1 invalid
                                $invalidMobiles =$invalidMobiles.' | '.$mobile;
                                //log as not added
                                log_message('info',$mobile.' INVALID MOBILE! IGNORED ENTRY: '.$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town);
                            }else{

                                //save farmer  ($name,$mobile,$email,$town_id,$region_id)
                                $farmer_added = $this->farmers_m->add_farmer($farmerName,$mobile,$email,$existsTown->id,$existsRegion->id);

                                if($farmer_added){
                                    $addCounter++;
                                    //log the region added
                                    log_message('info',$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town.' ADDED SUCCESSFULLY! ');

                                }else{
                                    $notAdded = $notAdded. ' | '.$mobile.'-'.$farmerName;
                                    //log the region as not added
                                    log_message('info','FARMER NOT ADDED! '.$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town);
                                }

                            }
                        }


                    }else{
                        $notAddedTown =$notAddedTown. ' | '.$town;
                        //log the town doesnt exist
                        log_message('info','FARMER NOT ADDED! '.$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town.' TOWN DOESNT EXIST ');
                    }
                }else{
                    $notAddedRegion =$notAddedRegion. ' | '.$region;
                    //log the region doesnt exist
                    log_message('info','FARMER NOT ADDED! '.$farmerName.' '.$mobile.' '.$email.' '.$region.' '.$town.' REGION DOESNT EXIST ');
                }



            }
        }
        $existingVariables ="";
        if(!empty($existingBizNames)){
            $existingVariables = 'Business Names: ('.$existingBizNames.') ';
        }

        if(!empty($existingMobiles)){
            $existingVariables =$existingVariables .'Mobile Numbers: ('.$existingMobiles.') ';
        }

        $invalidVariables ="";
        if(!empty($invalidMobiles)){
            $invalidVariables =$invalidVariables .'Mobiles: ('.$invalidMobiles.') ';
        }

        if(!empty($invalidEmails)){
            $invalidVariables =$invalidVariables .'Emails: ('.$invalidEmails.') ';
        }

        $notAddedVariables ="";
        if(!empty($notAddedRegion)){
            $notAddedVariables = 'NON EXISTING REGIONS: ('.$notAddedRegion.') ';
        }

        if(!empty($notAddedTown)){
            $notAddedVariables =$existingVariables .'NON EXISTING TOWNS: ('.$notAddedTown.') ';
        }


        $import_result = array('count'=>$addCounter,'existing'=>$existingVariables,'invalid'=>$invalidVariables,'notadded'=>$notAddedVariables);

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