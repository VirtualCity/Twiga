<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 8:52 AM
 */

class Detailed_purchases extends Admin_Controller{
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
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Detailed Purchases Report";
        $this->load->view('templates/header', $data);
        $this->load->view('reports/view_detailed_purchases',$data);
    }


    function datatable(){
        $this->datatables->select('purchase_products.id AS id,purchase_products.purchase_invoice_no as invoice_no,
        purchase_products.sku_code as sku_code,item_code,products.description as description,quantity,item_um,
        purchase_reports.msisdn as msisdn,customers.business_name,distributor_code,distributors.name AS distributor_name,
        regions.name AS region,towns.name AS town, purchase_reports.created AS created')
            ->unset_column('id')
            ->from('purchase_products')
            ->join('products','purchase_products.sku_code = products.sku_code')
            ->join('purchase_reports','purchase_products.purchase_report_id = purchase_reports.id')
            ->join('customers','purchase_reports.stockist_id = customers.id')
            ->join('distributors_codes','purchase_reports.distributor_code = distributors_codes.code')
            ->join('customers AS distributors','distributors_codes.customer_id = distributors.id')
            ->join('regions','customers.region_id = regions.id')
            ->join('towns','customers.town_id = towns.id');

        echo $this->datatables->generate();
    }
}
