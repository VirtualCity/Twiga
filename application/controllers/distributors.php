<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 9:30 AM
 */

class Distributors extends MY_Controller{

    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('distributors_m');
        $this->load->model('towns_m');
        $this->load->model('regions_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Distributors";
        $this->load->view('templates/header', $data);
        $this->load->view('distributors/view_distributors',$data);

    }

    function datatable_active(){
        $this->datatables->select('customers.id AS id,code,mobile1,business_name,customers.name AS name,email,towns.name AS town,regions.name AS region,customers.modified As modified,customers.created As created')
            ->unset_column('id')
            ->add_column('actions', get_active_distributors_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','ACTIVE')
	        ->where('customer_type','DISTRIBUTOR')
	        ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('regions', 'customers.region_id = regions.id')
	        ->join('towns', 'customers.town_id = towns.id')
	        ->join('distributors_codes', 'customers.id = distributors_codes.customer_id');
        echo $this->datatables->generate();
    }

    function datatable_inactive(){
        $this->datatables->select('customers.id AS id,code,mobile1,business_name,customers.name AS name,email,towns.name AS town,regions.name AS region,customers.modified As modified,customers.created As created')
            ->unset_column('id')
            ->add_column('actions', get_inactive_distributors_buttons('$1'), 'id')
            ->from('customers')
            ->where('status','INACTIVE')
	        ->where('customer_type','DISTRIBUTOR')
	        ->join('contacts_info', 'customers.contact_id = contacts_info.id')
            ->join('regions', 'customers.region_id = regions.id')
	        ->join('towns', 'customers.town_id = towns.id')
	        ->join('distributors_codes', 'customers.id = distributors_codes.customer_id');
        echo $this->datatables->generate();
    }

    function add(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('code', 'Distributor Code', 'required|max_length[30]|is_unique[distributors_codes.code]');
        $this->form_validation->set_rules('biz_name', 'Business Name', 'required|max_length[100]|is_unique[customers.business_name]');
        $this->form_validation->set_rules('name', 'Contact Person Name', 'max_length[100]');
        $this->form_validation->set_rules('mobile', 'Distributor Mobile', 'required|numeric|max_length[12]|is_unique[contacts_info.mobile1]|is_unique[contacts_info.mobile2]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');
        $this->form_validation->set_rules('region', 'Distributor Region', 'required');
        $this->form_validation->set_rules('town_id', 'town', 'required');
        $this->form_validation->set_rules('status', 'Distributor Status', 'required');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $code ="";
        $biz_name ="";
        $name ="";
        $mobile="";
        $email="";
        $region_id ="";
        $town_id ="";
        $status ="";


        // has the form been submitted
        if($this->input->post()){
	        $biz_name = trim($this->input->post('biz_name'));
            $code = trim($this->input->post('code'));
            $name = trim($this->input->post('name'));
            $mobile = ($this->input->post('mobile'));
            $email = ($this->input->post('email'));
            $status = ($this->input->post('status'));
            $region_id = ($this->input->post('region'));
            $town_id = ($this->input->post('town_id'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){


                //Save new distributor
                $saved = $this->distributors_m->add_distributor(strtoupper($code),ucwords($biz_name), ucwords($name),$mobile,strtolower($email),$town_id,$region_id,$status);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Distributor added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('distributors/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Distributor NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('distributors/add');
                }
            }
        }
        //Retrieve regions
        $areas = $this->regions_m->get_all_regions();
        $data['areas'] = $areas;

        //Retrieve towns
        $towns = $this->towns_m->get_all_towns();
        $data['towns'] = $towns;

        $data['code']=$code;
	    $data['biz_name']=$biz_name;
        $data['name']=$name;
        $data['mobile']=$mobile;
        $data['status']=$status;
        $data['email']=$email;
        $data['region_id']=$region_id;
	    $data['town_id']=$town_id;

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Distributor"; //
        $this->load->view('templates/header', $data);
        $this->load->view('distributors/add_distributor',$data);

    }

    function edit($id=null){
        if(!empty($id)){
            //retrieve distributor to edit
            $to_edit = $this->distributors_m->get_distributor($id);

            //display reply view
            $data['id']=$id;
            $data['code']=$to_edit->code;
	        $data['biz_name']=$to_edit->business_name;
            $data['name']=$to_edit->name;
            $data['mobile']=$to_edit->mobile1;
            $data['email']=$to_edit->email;
            $data['region_id']=$to_edit->region_id;
	        $data['town_id']=$to_edit->town_id;
            $data['status']=$to_edit->status;

            //Retrieve regions
            $areas = $this->regions_m->get_all_regions();
            $data['areas'] = $areas;
        }else{
            //return fail. distributor code already in use
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('distributors');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Distributor";
        $this->load->view('templates/header', $data);
        $this->load->view('distributors/edit_distributor',$data);

    }

    function modify(){
        // SET VALIDATION RULES

        $this->form_validation->set_rules('code', 'Distributor Code', 'required|max_length[50]');
	    $this->form_validation->set_rules('biz_name', 'Business Name', 'required|max_length[100]');
        $this->form_validation->set_rules('name', 'Contact Name', 'max_length[100]');
        $this->form_validation->set_rules('mobile', 'Distributor Mobile', 'required|numeric|max_length[12]|is_unique[contacts_info.mobile1]|is_unique[contacts_info.mobile2]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');
        $this->form_validation->set_rules('region', 'Distributor Region', 'required');
	    $this->form_validation->set_rules('town_id', 'town', 'required');
        $this->form_validation->set_rules('status', 'Distributor Status', 'required|max_length[200]');

        // has the form been submitted
        if($this->input->post()){
            $id = trim($this->input->post('id'));
            $code = trim($this->input->post('code'));
	        $biz_name = trim($this->input->post('biz_name'));
            $name = trim($this->input->post('name'));
            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $region_id = $this->input->post('region');
	        $town_id = $this->input->post('town_id');
            $status = $this->input->post('status');

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify code if it exists other than modified field
                $code_exists = $this->distributors_m->verify_distributor_code($id,$code);

                if($code_exists){
                    //return fail. distributor code already in use
                    $this->session->set_flashdata('appmsg', 'This Distributor Code "'.$code.'" is already in use by a different Distributor');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('distributors/edit/'.$id);
                }else{
                    //verify biz name if it exists other than modified field
                    $name_exists = $this->distributors_m->verify_distributor_bizname($id,$biz_name);

                    if($name_exists){
                        //return fail. distributor name already in use
                        $this->session->set_flashdata('appmsg', 'This Business Name "'.$biz_name.'" is already in use');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('distributors/edit/'.$id);
                    }else{
                        //Verify mobile number
	                    $distributr_details = $this->distributors_m->get_distributor($id);
                        $mobile_exists = $this->distributors_m->verify_distributor_mobile($distributr_details->contact_id,$mobile);
                        if($mobile_exists){
                            //return fail. distributor mobile already in use
                            $this->session->set_flashdata('appmsg', 'This Mobile number"'.$mobile.'" is already in use.');
                            $this->session->set_flashdata('alert_type', 'alert-danger');
                            redirect('distributors/edit/'.$id);
                        }else{

                            //Save new distributor
                            $saved = $this->distributors_m->update_distributor($id,strtoupper($code),ucwords($biz_name),ucwords($name),$distributr_details->contact_id,$mobile,strtolower($email),$town_id,$region_id,$status);

                            if($saved){
                                // Display success message
                                $this->session->set_flashdata('appmsg', 'Distributor modified successfully!');
                                $this->session->set_flashdata('alert_type', 'alert-success');
                                redirect('distributors');

                            }else{
                                // Display fail message
                                $this->session->set_flashdata('appmsg', 'Distributor NOT modified! Check logs');
                                $this->session->set_flashdata('alert_type', 'alert-danger');
                                redirect('distributors');
                            }

                        }

                    }
                }

            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('distributors/edit/'.$id);
        }

        redirect('distributors');
    }

    function activate($id){

        $activated = $this->distributors_m->activate_distributor($id);
        if($activated){
            $this->session->set_flashdata('appmsg', 'Distributor successfully activated!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Distributor activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("distributors");
    }

    function deactivate($id){

        $deactivated = $this->distributors_m->deactivate_distributor($id);
        if($deactivated){
            $this->session->set_flashdata('appmsg', 'Distributor successfully deactivated!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            $this->session->set_flashdata('appmsg', 'Distributor activation failed! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-danger');
        }
        redirect("distributors");
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Distributors";
        $this->load->view('templates/header', $data);
        $this->load->view('distributors/import_distributors',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/distributors/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size']	= '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';
	    $config['encrypt_name']  = true;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $error = array('error' => $this->upload->display_errors());

            log_message('error','Error: File not imported. '.$error);
            // Display fail message
            $this->session->set_flashdata('appmsg', $error['error']);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('distributors/import');
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
            $this->session->set_flashdata('appmsg', 'Distributors imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('distributors/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        $this->load->model('regions_m');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/distributors/'.$fileName;

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
        $existingCodes = '';
        $existingBizNames = '';
        $existingMobiles = '';
        $existingEmails = '';
        $invalidMobiles = '';
        $invalidEmails = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $distributorsData = $rowData[0];
            $distributorCode = trim($distributorsData[0]);
	        $biz_name = trim($distributorsData[1]);
            $distributorName = trim($distributorsData[2]);
            $distributorMobile = trim($distributorsData[3]);
            $distributorEmail = trim($distributorsData[4]);
            $distributorRegion = trim($distributorsData[5]);
	        $distributorTown = trim($distributorsData[6]);

            if (!empty($distributorCode) AND !empty($biz_name) AND !empty($distributorMobile) AND !empty($distributorRegion) AND !empty($distributorTown) ) {

                // Check region if it exists
                $existRegion = $this->regions_m->get_region_by_name($distributorRegion);

                if($existRegion){
	                // Check if Town exists in the region
	                $existsTown = $this->towns_m->check_town_region($distributorTown, $existRegion->id);

	                if($existsTown){
// Check distributor code exists or not
		                $existsCode = $this->distributors_m->check_distributor_code($distributorCode);

		                if($existsCode){
			                //Distributor Code exists
			                $existingCodes =$existingCodes.' | '.$distributorCode;
			                //log as not added
			                log_message('info',$distributorCode.' EXISTS! IGNORED ENTRY: '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail);

		                }else{
			                // Check distributor name exists or not
			                $existsName = $this->distributors_m->check_distributor_name($distributorName);

			                if($existsName){
				                //Distributor Name exists
				                $existingNames =$existingBizNames.' | '.$distributorName;
				                //log as not added
				                log_message('info',$distributorName.' EXISTS! IGNORED ENTRY: '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail);
			                }else {
				                // Check distributor mobile exists or not
				                $existsMobile = $this->distributors_m->check_distributor_mobile($distributorMobile);

				                if($existsMobile){
					                //Distributor Mobile exists
					                $existingMobiles =$existingMobiles.' | '.$distributorMobile;
					                //log as not added
					                log_message('info',$distributorMobile.' EXISTS! IGNORED ENTRY: '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail);
				                }else {
					                $valid_mobile = $this->__checkMobile($distributorMobile);
					                if(!$valid_mobile){
						                //Distributor Mobile invalid
						                $invalidMobiles =$invalidMobiles.' | '.$distributorMobile;
						                //log as not added
						                log_message('info',$distributorMobile.'  INVALID MOBILE! IGNORED ENTRY: '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail);
					                }else{

						                //Fields Valid. Add the distributor
						                $distributor_added = $this->distributors_m->add_distributor(strtoupper($distributorCode),ucwords($biz_name),ucwords($distributorName),$distributorMobile,strtolower($distributorEmail),$existsTown->id,$existRegion->id,'ACTIVE');

						                if($distributor_added){
							                $addCounter++;
							                //log the region added
							                log_message('info',$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail.' ADDED SUCCESSFULLY! ');

						                }else{
							                $notAdded = $notAdded. ' | '.$distributorName;
							                //log the as not added
							                log_message('info','DISTRIBUTOR NOT ADDED! '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail);
						                }
					                }
				                }
			                }

		                }
	                }else{
		                $notAdded =$notAdded. ' | '.$biz_name;
		                //log the town doesnt exist
		                log_message('info','Distributor NOT ADDED! '.$distributorCode.' '.$biz_name.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail.' '.$distributorRegion.' '.$distributorTown.' TOWN DOESNT EXIST ');
	                }

                }else{
                    $notAdded =$notAdded. ' | '.$biz_name;
                    //log the region as not added
                    log_message('info','DISTRIBUTOR NOT ADDED! '.$distributorCode.' '.$distributorName.' '.$distributorMobile.' '.$distributorEmail.' REGION doesnt Exist ');
                }



            }
        }
        $existingVariables ="";
        if(!empty($existingCodes)){
            $existingVariables = 'Distributor Codes: ('.$existingCodes.') ';
        }

        if(!empty($existingNames)){
            $existingVariables =$existingVariables .'Distributor Names: ('.$existingNames.') ';
        }

        if(!empty($existingMobiles)){
            $existingVariables =$existingVariables .'Distributor Mobiles: ('.$existingMobiles.') ';
        }

        $invalidVariables ="";
        if(!empty($invalidMobiles)){
            $invalidVariables =$invalidVariables .'Distributor Mobiles: ('.$invalidMobiles.') ';
        }

        if(!empty($invalidEmails)){
            $invalidVariables =$invalidVariables .'Distributor Emails: ('.$invalidEmails.') ';
        }


        $import_result = array('count'=>$addCounter,'existing'=>$existingVariables,'invalid'=>$invalidVariables,'notadded'=>$notAdded);

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

	function get_towns($region){
		header('Content-Type: application/x-json; charset=utf-8');
		echo(json_encode($this->towns_m->get_towns_by_region($region)));
	}

}