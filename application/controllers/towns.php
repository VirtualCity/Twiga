<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Towns extends Admin_Controller{
    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('towns_m');
        $this->load->model('regions_m');
        $this->load->model('tas_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Towns";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/view_towns',$data);
    }

    function datatable(){
        $this->datatables->select('towns.id AS id,towns.name AS town,regions.name AS region, towns.modified AS modified,towns.created AS created')
            ->unset_column('id')
            ->add_column('actions', get_towns_buttons('$1'), 'id')
            ->from('towns')
            ->join('regions','towns.region_id = regions.id');

        echo $this->datatables->generate();
    }

    function add(){


        $town="";
        $region_id ="";

        // SET VALIDATION RULES
        $this->form_validation->set_rules('town', 'Town Name', 'required|max_length[50]|is_unique[towns.name]');
        $this->form_validation->set_rules('region_id', 'Region', 'required|numeric');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // has the form been submitted
        if($this->input->post()){
            $town = trim($this->input->post('town'));
            $region_id = trim($this->input->post('region_id'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //Save new town
                $saved = $this->towns_m->add_town($town,$region_id);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'Town added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('towns/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'Town NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('towns/add');
                }


            }
        }

        //Retrieve towns
        $regions = $this->regions_m->get_all_regions();
        $data['regions'] = $regions;

        $data['town'] =$town;
        $data['region_id'] =$region_id;
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Town";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/add_town',$data);
    }

    function edit($id=null){

        if(!empty($id)){
            //retrieve town to edit
            $to_edit = $this->towns_m->get_town($id);

            //display reply view
            $data['id']=$id;
            $data['town']=$to_edit->name;
            $data['region_id']=$to_edit->region_id;
        }else{
            // No town id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('towns');
        }

        //Retrieve regions
        $regions = $this->regions_m->get_all_regions();
        $data['regions'] = $regions;
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Town";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/edit_town',$data);

    }

    function modify(){

        // SET VALIDATION RULES
        $this->form_validation->set_rules('town', 'Town Name', 'required|max_length[50]');
        $this->form_validation->set_rules('region_id', 'Region', 'required|numeric');

        // has the form been submitted
        if($this->input->post()){

            $id = $this->input->post('id');
            $town = trim($this->input->post('town'));
            $region_id = trim($this->input->post('region_id'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify town name doesnt exist except current edited town
                $town_exists = $this->towns_m->verify_town($id,$town);

                if($town_exists){
                    //return fail. town name already in use
                    $this->session->set_flashdata('appmsg', 'This Town "'.$town.'" already exists');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('towns/edit/'.$id);
                }else{
                    //Save new town
                    $saved = $this->towns_m->update_town($id,ucfirst($town),$region_id);

                    if($saved){
                        // Display success message
                        $this->session->set_flashdata('appmsg', 'Town updated successfully!');
                        $this->session->set_flashdata('alert_type', 'alert-success');
                        redirect('towns');

                    }else{
                        // Display fail message
                        $this->session->set_flashdata('appmsg', 'Town NOT updated! Check logs');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('towns');
                    }
                }




            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('towns/edit/'.$id);
        }

        redirect('towns');
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Towns";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/import_towns',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/towns/';
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
            redirect('towns/import');
        }else{
            $data = array('upload_data' => $this->upload->data());

            foreach($data['upload_data'] as $item => $value){
                log_message('info','item: '.$item. ' value: '.$value);
            }

            $data2 =  $this->upload->data();
            $file_name= $data2['file_name'];

            $result = $this->import_excel($file_name);

            $importedNo =$result['count'];
            $existing_regions = $result['existing'];
            $notImported = $result['notadded'];

            // Display success message
            $this->session->set_flashdata('existing', $existing_regions);
            $this->session->set_flashdata('notimported', $notImported);
            $this->session->set_flashdata('appmsg', 'Towns imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('towns/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        $this->load->model('regions_m');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/towns/'.$fileName;

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
        $highestColumn = 'B';

        $addCounter = 0;
        $notAdded = '';
        $existingTowns = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $townData = $rowData[0];
            $townName = trim($townData[0]);
            $townRegion = trim($townData[1]);

            if ($townName != null AND $townRegion!=null) {
                log_message('info', 'Excel Town Name: ' . $townName . '. Region: ' . $townRegion);

                // Check region if it exists
                $existRegion = $this->regions_m->get_region_by_name($townRegion);

                if($existRegion){
                    // Check town if it exists
                    $exists = $this->towns_m->check_town($townName);

                    if($exists){
                        $existingTowns =$existingTowns.' | '.$townName;
                        //log the region as not added
                        log_message('info',$townName.' EXISTS! ');
                    }else{
                        //add the region
                        $town_added = $this->towns_m->add_town($townName,$existRegion->id);

                        if($town_added){
                            $addCounter++;
                            //log the region added
                            log_message('info',$townName.' ADDED SUCCESSFULLY! ');

                        }else{
                            $notAdded .= ' '.$townName;
                            //log the region as not added
                            log_message('info',$townName.' NOT ADDED! ');
                        }
                    }
                }



            }
        }

        $import_result = array('count'=>$addCounter,'existing'=>$existingTowns,'notadded'=>$notAdded);

        return $import_result;
    }


    function addtas($id=null){

        if(!empty($id)){
            //retrieve town to edit
            $town_details = $this->towns_m->get_town($id);
            $region_id = $town_details->region_id;
            $region_details = $this->regions_m->get_region($region_id);
            $area_tas = $this->tas_m->get_TasNotInTownID($id);

            //display reply view
            $data['id']=$id;
            $data['town']=$town_details->name;
            $data['region']=$region_details->name;
            $data['area_tas']=$area_tas;
        }else{
            // No town id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('towns');
        }


        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Assign TA's to Town";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/assign_town_tas',$data);

    }


    function assigntas(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('town', 'Town Name', 'required');
        $this->form_validation->set_rules('tas', 'Select Technical Assistants', 'required');

        // has the form been submitted
        if($this->input->post()){

            $town_id = $this->input->post('id');
            $town = $this->input->post('town');
            $tas = $this->input->post('tas');



            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){
                $town_details = $town_details = $this->towns_m->get_town($town_id);
                $region_id = $town_details->region_id;

                $count = 0;
                foreach( $tas as $ta){
                    //check if Tas exists in town
                    $ta_exists = $this->tas_m->check_tas_in_town($ta,$town_id);

                    if(!$ta_exists){
                        //Save new town TA
                        $saved = $this->tas_m->add_tas_to_town($ta,$town_id,$region_id);
                        if($saved){
                            $count++;
                        }
                    }

                }

                // Display success message
                $this->session->set_flashdata('appmsg', $count.' Technical Assistant(s) have being  successfully assigned to '.$town_details->name.' Town!');
                $this->session->set_flashdata('alert_type', 'alert-success');
                redirect('towns');



            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('towns/addtas/'.$town_id);
        }

        redirect('towns');
    }

    function tas($id=null){
        if(!empty($id)){
            //retrieve town details
            $town_details = $this->towns_m->get_town($id);
            $region_id = $town_details->region_id;
            $region_details = $this->regions_m->get_region($region_id);



            //purchase report details
            $data['id']=$id;
            $data['region_name']=$region_details->name;
            $data['town_name']=$town_details->name;

        }else{
            //return fail.
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('towns');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "TA's in Town";
        $this->load->view('templates/header', $data);
        $this->load->view('towns/view_town_tas',$data);

    }

    function towns_tas($id){
        if(!empty($id)) {
            $this->datatables->select('area_tas.id AS id,area_tas.name as name,area_tas.mobile as mobile,area_tas.email as email,area_tas.division as division, area_tas.modified As modified,area_tas.created As created')
                ->unset_column('id')
                ->add_column('actions', get_towns_ta_buttons('$1',$id), 'id')
                ->from('towns_tas')
                ->join('area_tas', 'towns_tas.ta_id = area_tas.id')
                ->where('towns_tas.town_id',$id);


         //   log_message('info',$this->datatables->generate());
            echo $this->datatables->generate();


        }
    }


    function removeta($id,$town_id){
        if(!empty($id) AND !empty($town_id)) {
            $deleted = $this->tas_m->remove_tas_from_town($id,$town_id);
            if($deleted){
                //return success.
                $this->session->set_flashdata('appmsg', 'Technical Assistant has being removed from town');
                $this->session->set_flashdata('alert_type', 'alert-success');
                redirect('towns/tas/'.$town_id);
            } else{
                //return fail.
                $this->session->set_flashdata('appmsg', 'Failed to remove Technical assistant from town! Check logs');
                $this->session->set_flashdata('alert_type', 'alert-warning');
                redirect('towns/tas/'.$town_id);
            }


        }
    }
}