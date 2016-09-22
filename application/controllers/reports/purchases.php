<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Purchases extends Reports_Controller{
    function __construct(){
        parent::__construct();


        $this->load->helper('buttons_helper');
        $this->load->model('locations_m');
        $this->load->model('reports_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Purchases Report";
        $this->load->view('templates/header', $data);
        $this->load->view('reports/view_purchases',$data);
    }


    function datatable(){
        $this->datatables->select('purchase_reports.id AS pr_id,purchase_reports.msisdn AS msisdn,customers.business_name,towns.name AS town,regions.name AS region,distributor_code,distributors.name AS distributor_name,invoice_no,purchase_reports.created AS created')
            ->unset_column('pr_id')
            ->add_column('actions', get_view_purchases_buttons('$1'), 'pr_id')
            ->from('purchase_reports')
            ->join('customers','purchase_reports.stockist_id = customers.id')
            ->join('distributors_codes','purchase_reports.distributor_code = distributors_codes.code')
            ->join('customers AS distributors','distributors_codes.customer_id = distributors.id')
            ->join('regions','customers.region_id = regions.id')
            ->join('towns','customers.town_id = towns.id');
        echo $this->datatables->generate();
    }

    function products($id=null){
        if(!empty($id)){
            //retrieve purchase report details
            $purchase_report = $this->reports_m->get_purchase_report_details($id);


            log_message('info','purchase report'.var_export($purchase_report,true));

            //purchase report details
            $data['id']=$id;
            $data['mobile_used']=$purchase_report->msisdn;
            $data['stockist_mobile1']=$purchase_report->stockist_mobile1;
            $data['stockist_mobile2']=$purchase_report->stockist_mobile2;
            $data['stockist_town']=$purchase_report->town;
            $data['business_name']=$purchase_report->stockist_biz;
            $data['contact_name']=$purchase_report->contact_name;
            $data['invoice']=$purchase_report->invoice;
            $data['distributor_code']=$purchase_report->distributor_code;
            $data['distributor_mobile']=$purchase_report->distributor_mobile;
            $data['distributor_name']=$purchase_report->distributor_name;
            $data['distributor_region']=$purchase_report->region;
            $data['report_date']=$purchase_report->created;

        }else{
            //return fail. distributor code already in use
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('reports/purchases');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Distributor";
        $this->load->view('templates/header', $data);
        $this->load->view('reports/view_products',$data);

    }


    function datatable2($id){
        if(!empty($id)) {
            $this->datatables->select('purchase_products.id as pp_id,purchase_products.sku_code as sku_code,item_code,description,quantity,item_um')
                ->unset_column('pp_id')
                ->from('purchase_products')
                ->join('products','purchase_products.sku_code = products.sku_code','LEFT')
                ->where('purchase_products.purchase_report_id',$id);

            echo $this->datatables->generate();
        }
    }


}