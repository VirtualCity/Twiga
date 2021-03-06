<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Regions extends Admin_Controller{
    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('regions_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){


        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Regions";
        $this->load->view('templates/header', $data);
        $this->load->view('regions/view_regions',$data);
    }

    function datatable(){
        $this->datatables->select('id,name,description,modified,created')
            ->unset_column('id')
            ->add_column('actions', get_regions_buttons('$1'), 'id')
            ->from('regions');

        echo $this->datatables->generate();
    }

    function add(){


        $region="";
        $description="";

        // SET VALIDATION RULES
        $this->form_validation->set_rules('region', 'Region Name', 'required|max_length[50]|is_unique[regions.name]');
        $this->form_validation->set_rules('description', 'Region Description', 'max_length[200]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // has the form been submitted
        if($this->input->post()){
            $region = trim($this->input->post('region'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){


                //Save new region
                $saved = $this->regions_m->add_region($region,$description);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Region added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('regions/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Region NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('regions/add');
                }


            }
        }

        $data['region'] =$region;
        $data['description'] =$description;
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Region";
        $this->load->view('templates/header', $data);
        $this->load->view('regions/add_region',$data);
    }

    function edit($id=null){

        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->regions_m->get_region($id);

            //display reply view
            $data['id']=$id;
            $data['region']=$to_edit->name;
            $data['description']=$to_edit->description;
        }else{
            // No region id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('regions');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Region";
        $this->load->view('templates/header', $data);
        $this->load->view('regions/edit_region',$data);



    }

    function modify(){

        // SET VALIDATION RULES
        $this->form_validation->set_rules('region', 'Region Name', 'required|max_length[50]');
        $this->form_validation->set_rules('description', 'Region Description', 'max_length[200]');

        // has the form been submitted
        if($this->input->post()){

            $id = $this->input->post('id');
            $region = trim($this->input->post('region'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify region name doesnt exist except current edited region
                $region_exists = $this->regions_m->verify_region($id,$region);

                if($region_exists){
                    //return fail. region name already in use
                    $this->session->set_flashdata('appmsg', 'This Region name "'.$region.'" already exists');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('regions/edit/'.$id);
                }else{
                    //Save new region
                    $saved = $this->regions_m->update_region($id,$region,$description);

                    if($saved){
                        // Display success message
                        $this->session->set_flashdata('appmsg', 'Region updated successfully!');
                        $this->session->set_flashdata('alert_type', 'alert-success');
                        redirect('regions');

                    }else{
                        // Display fail message
                        $this->session->set_flashdata('appmsg', 'Region NOT updated! Check logs');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('regions');
                    }
                }




            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('regions/edit/'.$id);
        }

        redirect('regions');
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Regions";
        $this->load->view('templates/header', $data);
        $this->load->view('regions/import_regions',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/regions/';
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
            redirect('regions/import');
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
            $this->session->set_flashdata('appmsg', 'Regions imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('regions/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/regions/'.$fileName;

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'B';

        $addCounter = 0;
        $notAdded = '';
        $existingRegions = '';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $regionData = $rowData[0];
            $regionName = trim($regionData[0]);
            $regionDescription = trim($regionData[1]);

            if ($regionName != null) {
                log_message('info', 'Excel Region Name: ' . $regionName . ' Description: ' . $regionDescription);

                // Check region if it exists
                $exists = $this->regions_m->check_region($regionName);

                if($exists){
                    $existingRegions =$existingRegions.' | '.$regionName;
                    //log the region as not added
                    log_message('info',$regionName.' EXISTS! ');
                }else{
                    //add the region
                    $region_added = $this->regions_m->add_region($regionName,$regionDescription);

                    if($region_added){
                        $addCounter++;
                        //log the region added
                        log_message('info',$regionName.' ADDED SUCCESSFULLY! ');

                    }else{
                        $notAdded .= ' '.$regionName;
                        //log the region as not added
                        log_message('info',$regionName.' NOT ADDED! ');
                    }
                }

            }
        }

        $import_result = array('count'=>$addCounter,'existing'=>$existingRegions,'notadded'=>$notAdded);

        return $import_result;
    }


    /*Original Import */
   /* function import_excel($fileName){
        $this->load->library('Excel');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/regions/'.$fileName;

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        for ($row = 1; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            //  Insert row data array into your database of choice here

            log_message('info','Excel Dump: '. var_export($rowData,true));
        }

    }*/

}