<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Locations extends Admin_Controller{
    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('locations_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Locations";
        $this->load->view('templates/header', $data);
        $this->load->view('locations/view_locations',$data);
    }

    function datatable(){
        $this->datatables->select('id,name,description,modified,created')
            ->unset_column('id')
            ->add_column('actions', get_locations_buttons('$1'), 'id')
            ->from('locations');

        echo $this->datatables->generate();
    }

    function add(){


        $location="";
        $description="";

        // SET VALIDATION RULES
        $this->form_validation->set_rules('location', 'Location Name', 'required|max_length[50]|is_unique[locations.name]');
        $this->form_validation->set_rules('description', 'Location Description', 'max_length[200]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // has the form been submitted
        if($this->input->post()){
            $location = trim($this->input->post('location'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){


                //Save new location
                $saved = $this->locations_m->add_location($location,$description);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Location added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('locations/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Location NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('locations/add');
                }


            }
        }

        $data['location'] =$location;
        $data['description'] =$description;
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Location";
        $this->load->view('templates/header', $data);
        $this->load->view('locations/add_location',$data);
    }

    function edit($id=null){

        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->locations_m->get_location($id);

            //display reply view
            $data['id']=$id;
            $data['location']=$to_edit->name;
            $data['description']=$to_edit->description;
        }else{
            // No location id specified
            $this->session->set_flashdata('appmsg', 'An Error Was Encountered! No identifier provided ');
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('locations');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Location";
        $this->load->view('templates/header', $data);
        $this->load->view('locations/edit_location',$data);



    }

    function modify(){

        // SET VALIDATION RULES
        $this->form_validation->set_rules('location', 'Location Name', 'required|max_length[50]');
        $this->form_validation->set_rules('description', 'Location Description', 'max_length[200]');

        // has the form been submitted
        if($this->input->post()){
            /*todo check id field is not empty*/
            $id = $this->input->post('id');
            $location = trim($this->input->post('location'));
            $description = trim($this->input->post('description'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify location name doesnt exist except current edited location
                $location_exists = $this->locations_m->verify_location($id,$location);

                if($location_exists){
                    //return fail. location name already in use
                    $this->session->set_flashdata('appmsg', 'This Location name "'.$location.'" already exists');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('locations/edit/'.$id);
                }else{
                    //Save new location
                    $saved = $this->locations_m->update_location($id,$location,$description);

                    if($saved){
                        // Display success message
                        $this->session->set_flashdata('appmsg', 'Location updated successfully!');
                        $this->session->set_flashdata('alert_type', 'alert-success');
                        redirect('locations');

                    }else{
                        // Display fail message
                        $this->session->set_flashdata('appmsg', 'Location NOT updated! Check logs');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('locations');
                    }
                }




            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('locations/edit/'.$id);
        }

        redirect('locations');
    }



}