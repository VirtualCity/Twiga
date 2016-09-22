<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 10:12 AM
 */

class Reports_m extends CI_Model{

    function get_purchase_report_details($id){
        $this->db->select('purchase_reports.id AS id, purchase_reports.msisdn AS msisdn, contacts_info.mobile1 AS stockist_mobile1,
        contacts_info.mobile2 as stockist_mobile2, customers.name AS contact_name, customers.business_name AS stockist_biz,
        purchase_reports.invoice_no AS invoice,distributor_code, distributors_contacts.mobile1 as distributor_mobile,
        distributors.name AS distributor_name,regions.name AS region, towns.name AS town, purchase_reports.created AS created');
        $this->db->from('purchase_reports');
        $this->db->where('purchase_reports.id',$id);
        $this->db->join('customers','purchase_reports.stockist_id = customers.id');
        $this->db->join('contacts_info','customers.contact_id = contacts_info.id');
        $this->db->join('distributors_codes','purchase_reports.distributor_code = distributors_codes.code');
        $this->db->join('customers AS distributors','distributors_codes.customer_id = distributors.id');
        $this->db->join('contacts_info AS distributors_contacts','distributors.contact_id = distributors_contacts.id');
        $this->db->join('towns','customers.town_id = towns.id');
        $this->db->join('regions','distributors.region_id = regions.id');
        $query = $this -> db -> get();
        if ($query->num_rows() >0){
            return $query -> row();
        }else{
            return false;
        }

    }

    function get_received_sms($id){
        $this -> db-> select('*');
        $this -> db -> from('sms_received');
        $this-> db -> where('id',$id);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> row();
        }else{
            return false;
        }
    }

    function save_reply($msg_id,$msisdn,$biz_name,$msg,$reply,$user){
        $data =  array(
            'sent_to'=>$msisdn,
            'biz_name'=>$biz_name,
            'msg_id'=>$msg_id,
            'message'=>$msg,
            'reply'=>$reply,
            'sent_by'=>$user);
        $this->db->insert('replies',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){

            return true;
        }
        return false;
    }

    function change_reply_status($id){
        $data = array(
            'status' => 'REPLIED'
        );
        $this->db->where('id', $id);
        $query = $this->db->update('sms_received', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    /* Below functions used to retrieve reports for mailing to TAs and Managers*/
    function get_today_purchase_report(){
        $this->db->select('purchase_products.id AS id,purchase_products.purchase_invoice_no as invoice_no,purchase_products.sku_code as sku_code,item_code,products.description as description,quantity,item_um,purchase_reports.msisdn as msisdn,business_name,distributor_code,distributors.name AS distributor_name,stockists.region_id AS region,stockists.town_id AS town,purchase_reports.created AS created');
        $this->db->from('purchase_products');
        $this->db->where('DATE(purchase_products.created) >=( DATE(NOW()) - INTERVAL 7 DAY + INTERVAL 0 SECOND )');
        $this->db->where('customers.town_id = 2');
        $this->db->join('products','purchase_products.sku_code = products.sku_code');
        $this->db->join('purchase_reports','purchase_products.purchase_report_id = purchase_reports.id');
        $this->db->join('stockists','purchase_reports.stockist_id = stockists.id');
        $this->db->join('distributors','purchase_reports.distributor_code = distributors.code');
        /*$this->db->join('regions','distributors.region_id = regions.id');
        $this->db->join('towns','stockists.town_id = towns.id');*/
        $query = $this -> db -> get();
        if ($query->num_rows() >0){
            return $query -> result();
        }else{
            return false;
        }

    }

    //Get purchase report by town id
    function get_purchase_report_by_town($town_id){
        $this->db->select('purchase_products.id AS id,purchase_products.purchase_invoice_no as invoice_no,
        purchase_products.sku_code as sku_code,item_code,products.description as description,quantity,item_um,
        purchase_reports.msisdn as mobile,customers.business_name,customers.name,distributor_code,
        distributors.name AS distributor_name,purchase_reports.created AS date');
        $this->db->from('purchase_products');
        $this->db->where('DATE(purchase_products.created) >=( DATE(NOW()) - INTERVAL 7 DAY + INTERVAL 0 SECOND )');
        $this->db->where('customers.town_id = '.$town_id);
        $this->db->join('products','purchase_products.sku_code = products.sku_code');
        $this->db->join('purchase_reports','purchase_products.purchase_report_id = purchase_reports.id');
        $this->db->join('customers','purchase_reports.stockist_id = customers.id');
        $this->db->join('distributors_codes','purchase_reports.distributor_code = distributors_codes.code');
        $this->db->join('customers AS distributors','distributors_codes.customer_id = distributors.id');
        $query = $this -> db -> get();
        if ($query->num_rows() >0){
            return $query;
        }else{
            return false;
        }

    }

    //Get purchase report by region id
    function get_purchase_report_by_region($region_id){
        $this->db->select('purchase_products.id AS id,purchase_products.purchase_invoice_no as invoice_no,purchase_products.sku_code as sku_code,item_code,products.description as description,quantity,item_um,purchase_reports.msisdn as mobile,customers.business_name,customers.name,distributor_code,distributors.name AS distributor_name,towns.name AS town, purchase_reports.created AS date');
        $this->db->from('purchase_products');
        $this->db->where('DATE(purchase_products.created) >=( DATE(NOW()) - INTERVAL 7 DAY + INTERVAL 0 SECOND )');
        $this->db->where('customers.region_id = '.$region_id);
        $this->db->join('products','purchase_products.sku_code = products.sku_code');
        $this->db->join('purchase_reports','purchase_products.purchase_report_id = purchase_reports.id');
        $this->db->join('customers','purchase_reports.stockist_id = customers.id');
        $this->db->join('distributors_codes','purchase_reports.distributor_code = distributors_codes.code');
        $this->db->join('customers AS distributors','distributors_codes.customer_id = distributors.id');
        $this->db->join('towns','customers.town_id = towns.id');
        $query = $this -> db -> get();
        if ($query->num_rows() >0){
            return $query;
        }else{
            return false;
        }

    }

   /* //Get Managers by region id
    function get_managers_by_region($region){
        $this -> db-> select('*');
        $this -> db -> from('area_managers');
        $this-> db -> where('region_id',$region);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }
    //Get TA's by town_id and region_id
    function get_TAS_by_regionAndtown($region,$town){
        $this -> db-> select('*');
        $this -> db -> from('area_tas');
        $this-> db -> where('region_id',$region);
        $this-> db -> where('town_id',$town);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }*/

}