<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/3/14
 * Time: 7:00 PM
 */

class Users extends Admin_Controller{
    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('user_model');
        $this->load->database();

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Manage Users";
        $this->load->view('templates/header', $data);
        $this->load->view('users',$data);
    }
    //return activated users
    function active(){
        $this->datatables->select('id,username,fname,surname,oname,mobile,email,role,created')
            ->unset_column('id')
            ->add_column('actions', get_active_users_buttons('$1'), 'id')
            ->from('users')
            ->where('status','active');

         $result= $this->datatables->generate();
        echo $result;
        log_message('info','Datatables log: '.$result);

    }
    //return suspended users
    function suspended(){
        $this->datatables->select('id,username,fname,surname,oname,mobile,email,role,created')
            ->unset_column('id')
            ->add_column('actions', get_suspended_users_buttons('$1'), 'id')
            ->from('users')
            ->where('status','suspended');

        echo $this->datatables->generate();
    }

    function suspend($id){
        if(!$this->session->userdata('logged_in')){
            redirect('login');
            //echo("You are NOT logged in");
        }

        $current_user_id = $this->session->userdata('id');

        if($current_user_id==$id){
            log_message('info', 'User account: '.$id.' not allowed to suspend own account');

            $this->session->set_flashdata('appmsg', 'You cannot suspend your own account!');
            $this->session->set_flashdata('alert_type', 'alert-warning');

        }else{
            $suspended = $this-> user_model -> suspend_user($id);
            if($suspended){
                $this->session->set_flashdata('appmsg', 'User account successfully suspended!');
                $this->session->set_flashdata('alert_type', 'alert-success');
            }else{
                $this->session->set_flashdata('appmsg', 'Failed to suspend user account! Check logs.');
                $this->session->set_flashdata('alert_type', 'alert-warning');
            }
        }

        redirect("users");
    }

    function activate($id){
        if(!$this->session->userdata('logged_in')){
            redirect('login');
            //echo("You are NOT logged in");
        }

        $activated = $this->user_model->activate_user($id);
        if($activated){
            log_message('info', 'User account: '.$id.' has been activated');
            $this->session->set_flashdata('appmsg', 'User account successfully activated!');
            $this->session->set_flashdata('alert_type', 'alert-success');
        }else{
            log_message('info', 'User account: '.$id.' failed to activate');
            $this->session->set_flashdata('appmsg', 'Failed to activate user account! Check logs.');
            $this->session->set_flashdata('alert_type', 'alert-warning');
        }
        redirect("users");
    }

    function reset(){
		$this->form_validation->set_rules('id', 'ID', 'required|numeric');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[50]');


        log_message("info","reset called");
        if($this->input->post()){
            log_message("info","method is post");
            $user_id = $this->input->post('id');
            $pass = $this->input->post('password');

            //check if user id
            log_message("info","user id: ".$user_id." Password: ".$pass);

            if($this->form_validation->run()){

                //Save new password
                $saved = $this->user_model->save_password($user_id,$pass);

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'User account has been reset successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'User account failed to reset!');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                }

            }else{
                $this->session->set_flashdata('appmsg', 'Reset Password invalid! Required password must be between 8 to 50 characters.');
                $this->session->set_flashdata('alert_type', 'alert-warning');
            }

            redirect("users");
        }
    }


}