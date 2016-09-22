<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Groups extends Admin_Controller{
    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('groups_model');
        $this->load->model('contacts_model');
        $this->load->model('stockists_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "SMS Groups";
        $this->load->view('templates/header', $data);
        $this->load->view('groups/view_groups',$data);
    }

    function datatable(){
        $this->datatables->select('id,name,description,created')
            ->unset_column('id')
            ->add_column('actions', get_groups_buttons('$1'), 'id')
            ->from('groups');

        echo $this->datatables->generate();
    }

    function add(){


        $group="";
        $description ="";

        // SET VALIDATION RULES
        $this->form_validation->set_rules('group', 'Group Name', 'required|alpha_numeric|min_length[3]|max_length[30]|is_unique[groups.name]');
        $this->form_validation->set_rules('description', 'Description', 'max_length[200]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // has the form been submitted
        if($this->input->post()){
            $group = trim($this->input->post('group'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //Save new town
                $saved =  $this->groups_model->add_group(ucfirst($group),$description);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'Group has been successfully added!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('groups/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'Group has NOT added. Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('groups/add');
                }

            }
        }



        $data['group'] =$group;
        $data['description'] =$description;

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add SMS Group";
        $this->load->view('templates/header', $data);
        $this->load->view('groups/add_group',$data);
    }

    function contacts($id=null){
        if(!empty($id)){

            //retrieve group name
            $group = $this->groups_model->get_group_by_id($id);
            $data['group_name'] = $group->name;


            $data['groupid']=$id;
            $data['user_role'] = $this->session->userdata('role');
            $data['title'] = "Group Contacts";
            $this->load->view('templates/header', $data);
            $this->load->view('groups/view_group_contacts',$data);
        }else{
            // No group id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No Group identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('groups');
        }

    }

    function datatable2($id){
        $this->datatables->select('group_contacts.id as id,group_contacts.msisdn AS mobile,business_name,name,email,group_contacts.created as created')
            ->unset_column('id')
            ->from('group_contacts')
            ->where('group_contacts.groupid',$id)
            ->join('contacts_info','group_contacts.msisdn=contacts_info.mobile1 OR group_contacts.msisdn=contacts_info.mobile2')
            ->join('customers','contacts_info.id=customers.contact_id');

        echo $this->datatables->generate();
    }

    function edit($id=null){
        log_message('info','Edit id :'.$id);
        if(!empty($id)){
            //check if group already has contacts
            $contacts = $this->contacts_model->get_group_contacts($id);

            if(!$contacts){
                //retrieve group to edit
                $to_edit = $this->groups_model->get_group_by_id($id);

                //display reply view
                $data['id']=$id;
                $data['group']=$to_edit->name;
                $data['description']=$to_edit->description;
            }else{
                // No group id specified
                $this->session->set_flashdata('appmsg', 'Group cannot be edited because it already contains subscribed numbers');
                $this->session->set_flashdata('alert_type', 'alert-danger');
                redirect('groups');
            }


        }else{
            // No group id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No Group identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('groups');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Group";
        $this->load->view('templates/header', $data);
        $this->load->view('groups/edit_group',$data);

    }

    function modify(){

        // SET VALIDATION RULES
        $this->form_validation->set_rules('group', 'Group Name', 'required|alpha_numeric|min_length[3]|max_length[30]');
        $this->form_validation->set_rules('description', 'Description', 'max_length[200]');

        // has the form been submitted
        if($this->input->post()){

            $id = $this->input->post('id');
            $group = trim($this->input->post('group'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify group name doesnt exist except current edited group
                $group_exists = $this->groups_model->verify_group($id,$group);

                if($group_exists){
                    //return fail. group name already in use
                    $this->session->set_flashdata('appmsg', 'This group "'.$group.'" already exists');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('groups/edit/'.$id);
                }else{
                    //Save new group
                    $saved = $this->groups_model->update_group($id,ucfirst($group),$description);

                    if($saved){
                        // Display success message
                        $this->session->set_flashdata('appmsg', 'Group updated successfully!');
                        $this->session->set_flashdata('alert_type', 'alert-success');
                        redirect('groups');

                    }else{
                        // Display fail message
                        $this->session->set_flashdata('appmsg', 'Group NOT updated! Check logs');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('groups');
                    }
                }

            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('groups/edit/'.$id);
        }

        redirect('groups');
    }

    function import(){
        $groups = $this->groups_model->get_groups_except_stockist();

        $data['groups']=$groups;
        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Stockist to Group";
        $this->load->view('templates/header', $data);
        $this->load->view('groups/import_stockists',$data);
    }

    function do_upload(){

       // $this->form_validation->set_rules('userfile', 'Import File', 'required');
        $this->form_validation->set_rules('group', 'group', 'required');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if($this->input->post('group')!==""){
            $group_id = $this->input->post('group');

            $config['upload_path'] = './uploads/groups/';
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
                redirect('groups/import');
            }else{
                $data = array('upload_data' => $this->upload->data());

                foreach($data['upload_data'] as $item => $value){
                    log_message('info','item: '.$item. ' value: '.$value);
                }

                $data2 =  $this->upload->data();
                $file_name= $data2['file_name'];

                $result = $this->import_excel($file_name,$group_id);

                $importedNo =$result['count'];
                $existing = $result['existing'];
                $unregistered = $result['existing'];
                $notImported = $result['notadded'];

                // Display success message
                $this->session->set_flashdata('existing', $existing);
                $this->session->set_flashdata('notimported', $notImported);
                $this->session->set_flashdata('unregistered', $unregistered);
                $this->session->set_flashdata('appmsg', 'Stockists imported: '.$importedNo);
                $this->session->set_flashdata('alert_type', 'alert-success');
                redirect('groups/import');
            }


        }else{
            // Display fail message
            $this->session->set_flashdata('appmsg','Import Failed! Browse and select file to import then choose a target group');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('groups/import');
        }



    }

    function import_excel($fileName,$group_id){
        $this->load->library('Excel');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/groups/'.$fileName;

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
        $highestColumn = 'A';

        $addCounter = 0;
        $existingContacts = '';
        $unknownContacts ='';
        $notAdded='';

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $groupData = $rowData[0];
            $mobile = trim($groupData[0]);

            //Check if all fields are not null
            if($mobile!=null){

                // Check mobile belongs to stockist in the system
                $existsMobile = $this->stockists_m->check_mobile($mobile);

                if($existsMobile){
                    //check if it is already added to group contacts
                    $registered = $this->contacts_model->check_subscribed_contact($group_id,$mobile);

                    if($registered){
                        //Mobile number already in group
                        log_message('info',$mobile.' EXISTS IN GROUP! ');
                        if($existingContacts !=""){
                            $existingContacts = $existingContacts.' | '.$mobile;
                        }else{
                            $existingContacts = $mobile;
                        }
                    }else{
                        //Add mobile to group
                        $mobile_added = $this->contacts_model->add_contact_togroup_viaId($mobile,$group_id);

                        if($mobile_added){
                            $addCounter++;
                            //Mobile added successfully

                        }else{
                            if($notAdded !=""){
                                $notAdded = $notAdded.' | '.$mobile;
                            }else{
                                $notAdded = $mobile;
                            }

                            //log the region as not added
                            log_message('info','MOBILE NOT ADDED! '.$mobile);
                        }

                    }

                }else{
                    log_message('info',$mobile.' Does not match any stockist/Distributr/Farmer number in system! ');
                    if($unknownContacts !=""){
                        $unknownContacts = $unknownContacts.' | '.$mobile;
                    }else{
                        $unknownContacts = $mobile;
                    }
                }


            }

        }

        $existingVariables ="";
        $unregisteredVariables ="";

        if(!empty($existingContacts)){
            $existingVariables = 'Mobile Numbers: ('.$existingContacts.') ';
        }

        if(!empty($unknownContacts)){
            $unregisteredVariables =$unregisteredVariables .'Descriptions: ('.$unknownContacts.') ';
        }




        $import_result = array('count'=>$addCounter,'existing'=>$existingVariables,'unregistered'=>$unregisteredVariables,'notadded'=>$notAdded);

        return $import_result;
    }



}