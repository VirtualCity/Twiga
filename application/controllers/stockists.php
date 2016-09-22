<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 9:30 AM
 */

class Stockists extends Admin_Controller{

    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('stockists_m');
        $this->load->model('towns_m');
        $this->load->model('regions_m');
        $this->load->model('auditlog_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Stockists";
        $this->load->view('templates/header', $data);
        $this->load->view('stockists/view_stockists',$data);

    }

    function datatable_active(){
        $this->datatables->select('customers.id AS id,mobile1,mobile2,business_name,customers.name as name,email,towns.name AS town,regions.name AS region,customers.modified,customers.created')
            ->unset_column('id')
            ->add_column('actions', get_active_stockists_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','ACTIVE')
            ->where('customer_type','STOCKIST')
            ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('towns', 'customers.town_id = towns.id')
            ->join('regions', 'customers.region_id = regions.id');
        echo $this->datatables->generate();
    }

    function datatable_suspended(){
        $this->datatables->select('customers.id AS id,mobile1,mobile2,business_name,customers.name as name,email,towns.name AS town,regions.name AS region,customers.modified,customers.created')
            ->unset_column('id')
            ->add_column('actions', get_inactive_stockists_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','SUSPENDED')
            ->where('customer_type','STOCKIST')
            ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('towns', 'customers.town_id = towns.id')
            ->join('regions', 'customers.region_id = regions.id');
        echo $this->datatables->generate();
    }

    function add(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('biz_name', 'Business Name', 'required|max_length[100]|is_unique[customers.business_name]');
        $this->form_validation->set_rules('stockist_name', 'Stockist Name', 'required|max_length[100]');
        $this->form_validation->set_rules('mobile1', 'Mobile 1', 'required|numeric|max_length[12]|is_unique[contacts_info.mobile1]|is_unique[contacts_info.mobile2]');
        $this->form_validation->set_rules('mobile2', 'Mobile 2', 'numeric|max_length[12]|is_unique[contacts_info.mobile2]|is_unique[contacts_info.mobile1]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[150]');
        $this->form_validation->set_rules('region_id', 'region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $biz_name ="";
        $stockist_name="";
        $mobile1 ="";
        $mobile2 ="";
        $email="";
        $town_id ="";
        $region_id ="";

        // has the form been submitted
        if($this->input->post()){
            $biz_name = trim($this->input->post('biz_name'));
            $stockist_name = trim($this->input->post('stockist_name'));
            $mobile1 = ($this->input->post('mobile1'));
            $mobile2 = ($this->input->post('mobile2'));
            $email = $this->input->post('email');
            $region_id = ($this->input->post('region_id'));
            $town_id = ($this->input->post('town_id'));


            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //Save new distributor
                $saved = $this->stockists_m->add_stockist(ucwords($biz_name),ucwords($stockist_name),$mobile1,$mobile2,strtolower($email),$town_id,$region_id);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Stockist added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('stockists/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Stockist NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('stockists/add');
                }
            }
        }

        //Retrieve regions
        $regions = $this->regions_m->get_all_regions();
        $data['regions'] = $regions;

        //Retrieve towns
        $towns = $this->towns_m->get_all_towns();
        $data['towns'] = $towns;


        $data['biz_name']=$biz_name;
        $data['stockist_name']=$stockist_name;
        $data['mobile1']=$mobile1;
        $data['mobile2']=$mobile2;
        $data['email']=$email;
        $data['region_id']=$region_id;
        $data['town_id']=$town_id;


        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Stockist";
        $this->load->view('templates/header', $data);
        $this->load->view('stockists/add_stockist',$data);

    }

    function edit($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->stockists_m->get_stockist($id);

            //display reply view
            $data['id']=$id;
            $data['biz_name']=$to_edit->business_name;
            $data['stockist_name']=$to_edit->name;
            $data['mobile1']=$to_edit->mobile1;
            $data['mobile2']=$to_edit->mobile2;
            $data['email']=$to_edit->email;
            $data['region_id']=$to_edit->region_id;
            $data['town_id']=$to_edit->town_id;

            //Retrieve regions
            $regions = $this->regions_m->get_all_regions();
            $data['regions'] = $regions;
        }else{
            //return to stockist. no id specified
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('stockists');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Stockist";
        $this->load->view('templates/header', $data);
        $this->load->view('stockists/edit_stockist',$data);

    }

    function modify(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('biz_name', 'Business Name', 'required|max_length[100]');
        $this->form_validation->set_rules('stockist_name', 'Stockist Name', 'required|max_length[100]');
        $this->form_validation->set_rules('mobile1', 'Mobile 1', 'required|numeric|max_length[12]');
        $this->form_validation->set_rules('mobile2', 'Mobile 2', 'numeric|max_length[12]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[150]');
        $this->form_validation->set_rules('region_id', 'region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');

        // has the form been submitted
        if($this->input->post()){
            $id = $this->input->post('id');
            $biz_name = trim($this->input->post('biz_name'));
            $stockist_name = trim($this->input->post('stockist_name'));
            $mobile1 = $this->input->post('mobile1');
            $mobile2 = $this->input->post('mobile2');
            $email = $this->input->post('email');
            $region_id = $this->input->post('region_id');
            $town_id = $this->input->post('town_id');

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify name if it exists other than modified field
                $name_exists = $this->stockists_m->verify_stockist_bizname($id,$biz_name);

                if($name_exists){
                    //return fail. stockist name already in use
                    $this->session->set_flashdata('appmsg', 'This Stockist Business Name "'.$biz_name.'" is already in use by a different stockist');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('stockists/edit/'.$id);
                }else{
                    $stockist_details = $this->stockists_m->get_stockist($id);
                    //Verify mobile number1
                    $mobile1_exists = $this->stockists_m->verify_stockist_mobile($stockist_details->contact_id,$mobile1);

                    if($mobile1_exists){
                        //return fail. stockist mobile1 already in use
                        $this->session->set_flashdata('appmsg', 'This Mobile #1 "'.$mobile1.'" is already in use');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('stockists/edit/'.$id);
                    }else{
                        //Verify mobile number2
                        $mobile2_exists =false;
                        if(!empty($mobile2)) {
                            $mobile2_exists = $this->stockists_m->verify_stockist_mobile($stockist_details->contact_id, $mobile2);
                            log_message('info','Mobile 2 exists');
                        }
                        if($mobile2_exists){
                            //return fail. stockist mobile1 already in use
                            $this->session->set_flashdata('appmsg', 'This Mobile #2 "'.$mobile2.'" is already in use');
                            $this->session->set_flashdata('alert_type', 'alert-danger');
                            redirect('stockists/edit/'.$id);
                        }else{
                            //update stockist
                            $saved = $this->stockists_m->update_stockist($id,ucwords($biz_name),ucwords($stockist_name),$stockist_details->contact_id,$mobile1,$mobile2,strtolower($email),$town_id,$region_id);

                            if($saved){
                                // Display success message
                                $this->session->set_flashdata('appmsg', 'Stockist modified successfully!');
                                $this->session->set_flashdata('alert_type', 'alert-success');
                                redirect('stockists');

                            }else{
                                // Display fail message
                                $this->session->set_flashdata('appmsg', 'Stockist NOT modified! Check logs');
                                $this->session->set_flashdata('alert_type', 'alert-danger');
                                redirect('stockists');
                            }
                        }
                    }

                }

            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('stockists/edit/'.$id);
        }

        redirect('stockists');
    }

    function sms($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_sms = $this->stockists_m->get_stockist($id);

            //display reply view
            $data['id']=$id;
            $data['biz_name']=$to_sms->business_name;
            $data['stockist_name']=$to_sms->name;
            $data['mobile1']=$to_sms->mobile1;
            $data['mobile2']=$to_sms->mobile2;
            $data['mobiles'] = array($to_sms->mobile1,$to_sms->mobile2);
            $data['message'] ="";

        }else{
            //return to stockist. No id specified
            $this->session->set_flashdata('appmsg', 'Error encountered! No SMS identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('stockists');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "SMS Stockist";
        $this->load->view('templates/header', $data);
        $this->load->view('stockists/sms_stockist',$data);
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
            $recipient = $this->input->post('biz');
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

                    redirect('stockists');
                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'Message to '.$recipient.'('.$msisdn.') failed.');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('stockists');
                }


            }else{
                $errors = validation_errors();
                $this->session->set_flashdata('appmsg', $errors);
                $this->session->set_flashdata('alert_type', 'alert-danger');
                redirect('stockists/sms/'.$id);
            }
        }

        redirect('stockists');
    }

    function activate($id){

        $activated = $this->stockists_m->activate_stockist($id);
        if($activated){
            $this->session->set_flashdata('appmsg', 'Stockist successfully activated!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Stockist activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("stockists");
    }

    function suspend($id){

        $deactivated = $this->stockists_m->suspend_stockist($id);
        if($deactivated){
            $this->session->set_flashdata('appmsg', 'Stockist successfully suspended!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Stockist activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("stockists");
    }

    function get_towns($region){
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this->towns_m->get_towns_by_region($region)));
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Stockists";
        $this->load->view('templates/header', $data);
        $this->load->view('stockists/import_stockists',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/stockists/';
        $config['allowed_types'] = 'xls|xlsx';
       // $config['overwrite'] = TRUE;
        $config['max_size']	= '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $error = array('error' => $this->upload->display_errors());

            log_message('error','Error: File not imported. '.$error);
            // Display fail message
            $this->session->set_flashdata('appmsg', $error['error']);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('stockists/import');
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
            $this->session->set_flashdata('appmsg', 'Stockists imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('stockists/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        $this->load->model('regions_m');
        $this->load->model('towns_m');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/stockists/'.$fileName;

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
        $highestColumn = 'I';

        $addCounter = 0;
        $notAdded = '';
        $notAddedTown = '';
        $notAddedRegion = '';
        $existingBizNames = '';
        $existingStockistNames = '';
        $existingMobiles = '';
        $invalidMobiles = '';
        $invalidEmails = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $stockistData = $rowData[0];
            $businessName = trim($stockistData[0]);
            $stockistFName = $stockistData[1];
            $stockistMName = $stockistData[2];
            $stockistLName = $stockistData[3];
            $mobile1 = trim($stockistData[4]);
            $mobile2 = trim($stockistData[5]);
            $email = trim($stockistData[6]);
            $town = trim($stockistData[7]);
            $region = trim($stockistData[8]);


            $stockistName =trim($stockistFName.' '.$stockistMName.' '.$stockistLName);

            if ($businessName != null AND $stockistName!=null AND $mobile1!=null AND $region!=null AND $town!=null ) {
                log_message('info', 'Business Name: ' .$businessName .' Stockist Name: ' . $stockistName .'. Mobile1: ' . $mobile1.'. Mobile2: ' . $mobile2. '. Email: ' . $email. '. Town: ' . $town);

                // Check region if it exists
                $existsRegion = $this->regions_m->get_region_by_name($region);
                log_message('info', 'Region: '.print_r($existsRegion));
                if($existsRegion){
                    // Check if Town exists in the region
                    $existsTown = $this->towns_m->check_town_region($town, $existsRegion->id);

                    if($existsTown){
                        //Check Business Name
                        $existsBiz = $this->stockists_m->check_stockist_bizname($businessName);

                        if($existsBiz){
                            //Business name exists
                            $existingBizNames =$existingBizNames.' | '.$businessName;
                            //log as not added
                            //log_message('info',$businessName.' EXISTS! IGNORED ENTRY: '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                            $this->auditlog_m->log_import("DUPLICATE","Business name already exists", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                        }else{
                            // Check Mobile1 name exists or not
                            $existsMobile1 = $this->stockists_m->check_mobile($mobile1);

                            if($existsMobile1){
                                //Mobile1 exists
                                $existingMobiles =$existingMobiles.' | '.$mobile1;
                                //log as not added
                                //log_message('info',$mobile1.' EXISTS! IGNORED ENTRY: '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                $this->auditlog_m->log_import("DUPLICATE","Mobile 1 already exists", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                            }else {
                                //check mobile validity
                                $valid_mobile = $this->__checkMobile($mobile1);

                                if(!$valid_mobile){
                                    //Mobile1 invalid
                                    $invalidMobiles =$invalidMobiles.' | '.$mobile1;
                                    //log as not added
                                    //log_message('info',$mobile1.' INVALID MOBILE! IGNORED ENTRY: '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                    $this->auditlog_m->log_import("INVALID_MOBILE","Invalid format for Mobile 1", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                                }else{
                                    // Check if mobile2 is empty
                                    if($mobile2 != null){
                                        //check if it exists
                                        $existsMobile2 = $this->stockists_m->check_mobile($mobile2);

                                        if($existsMobile2){
                                            //Mobile1 exists
                                            $existingMobiles =$existingMobiles.' | '.$mobile2;
                                            //log as not added
                                           // log_message('info',$mobile2.' EXISTS! IGNORED ENTRY: '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                            $this->auditlog_m->log_import("DUPLICATE","Mobile 2 already exists", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                                        }else{
                                            //check if it is valid
                                            $valid_mobile2 = $this->__checkMobile($mobile2);

                                            if(!$valid_mobile2){
                                                //Mobile2 invalid
                                                $invalidMobiles =$invalidMobiles.' | '.$mobile2;
                                                //log as not added
                                                //log_message('info',$mobile2.' INVALID MOBILE! IGNORED ENTRY: '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                                $this->auditlog_m->log_import("INVALID_MOBILE","Invalid format for Mobile 2", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                                            }else{
                                                $stockist_added = $this->stockists_m->add_stockist(ucwords($businessName),ucwords($stockistName),$mobile1,$mobile2,strtolower($email),$existsTown->id,$existsRegion->id);

                                                if($stockist_added){
                                                    $addCounter++;
                                                    //log the region added
                                                    //log_message('info',$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town.' ADDED SUCCESSFULLY! ');

                                                }else{
                                                    $notAdded = $notAdded. ' | '.$businessName;
                                                    //log the region as not added
                                                    log_message('info','STOCKIST NOT ADDED! '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                                    $this->auditlog_m->log_import("FAILED","Stockist not Added", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                                                }
                                            }

                                        }
                                    }else{

                                        //save stockist  add_stockist($biz_name,$stockist_name,$mobile1,$mobile2,$email,$town_id,$region_id)
                                        $stockist_added = $this->stockists_m->add_stockist(ucwords($businessName),ucwords($stockistName),$mobile1,$mobile2,strtolower($email),$existsTown->id,$existsRegion->id);

                                        if($stockist_added){
                                            $addCounter++;

                                        }else{
                                            $notAdded = $notAdded. ' | '.$businessName;
                                            //log the region as not added
                                            log_message('info','STOCKIST NOT ADDED! '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town);
                                            $this->auditlog_m->log_import("FAILED","Stockist not Added", $businessName.'-'.$stockistName.'-'.$mobile1.'-'.$mobile2.'-'.$email.'-'.$region.'-'.$town);
                                        }


                                    }

                                }
                            }

                        }
                    }else{
                        $notAddedTown =$notAddedTown. ' | '.$town;
                        //log the town doesnt exist
                        log_message('info','STOCKIST NOT ADDED! '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town.' TOWN DOESNT EXIST ');
                    }
                }else{
                    $notAddedRegion =$notAddedRegion. ' | '.$region;
                    //log the region doesnt exist
                    log_message('info','STOCKIST NOT ADDED! '.$businessName.' '.$stockistName.' '.$mobile1.' '.$mobile2.' '.$email.' '.$region.' '.$town.' REGION DOESNT EXIST ');
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