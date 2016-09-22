<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 7/30/14
 * Time: 3:50 PM
 */

class Dashboard extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('dashboard_model');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    }

    function index(){

        if(!$this->session->userdata('logged_in')){
            redirect('/login');
            //echo("You are NOT logged in");
        }
        log_message("info", "Role: ". $this->session->userdata('role'));

        //Retrieve Dashboard variables
        $today_totals = $this->dashboard_model->get_todays_total();
        $weeks_total = $this->dashboard_model->get_weeks_total();
        $months_total = $this->dashboard_model->get_months_total();
        $stockists_total = $this->dashboard_model->get_stockists_total();
        $distributors_total = $this->dashboard_model->get_distributors_total();
        $farmers_total = $this->dashboard_model->get_farmers_total();
        $groups_total = $this->dashboard_model->get_contact_groups_total();
        $outbox_total = $this->dashboard_model->get_outbox_total();
        $products_total = $this->dashboard_model->get_products_total();
        $blacklist_total = $this->dashboard_model->get_blacklist_total();


        //Add dashvboards variables in data array
        $data['todays_total']=$today_totals;
        $data['weeks_total']=$weeks_total;
        $data['months_total']=$months_total;
        $data['stockists_total']=$stockists_total;
        $data['distributors_total']=$distributors_total;
        $data['farmers_total']=$farmers_total;
        $data['groups_total']=$groups_total;
        $data['outbox_total']=$outbox_total;
        $data['blacklist_total']=$blacklist_total;
        $data['products_total']=$products_total;

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Dashboard";
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard',$data);
    }

}