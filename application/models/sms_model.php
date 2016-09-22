<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 7/31/14
 * Time: 11:26 PM
 */

class Sms_model extends CI_Model{


    //Save purchase report from stockist
    function save_purchase_report($stockist_id,$msisdn,$distributor_code,$invoice_no){

        $data =  array(
            'stockist_id'=>$stockist_id,
            'msisdn'=>$msisdn,
            'distributor_code'=>$distributor_code,
            'invoice_no'=>$invoice_no);
        $this->db->set('purchase_date', 'NOW()', FALSE);
        $this->db->insert('purchase_reports',$data);
       // $num_insert = $this->db->affected_rows();
        if($this->db->insert_id()){
            return $this->db->insert_id();
        }
        return false;
    }

    //Save purchased products ave_purchase_products($saved_id,$invoice,$prd_sku_code,$prd_qty)
    function save_purchase_products($purchase_id,$invoice_no,$sku_code,$quantity){

        $data =  array(
            'purchase_report_id'=>$purchase_id,
            'purchase_invoice_no'=>$invoice_no,
            'sku_code'=>$sku_code,
            'quantity'=>$quantity);
        $this->db->insert('purchase_products',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }


    function save_received_sms($customer_id,$msisdn,$business_name,$name,$customer_type,$message,$message_type,$group,$status){

        $data =  array(
            'sender_id'=>$customer_id,
            'msisdn'=>$msisdn,
            'business_name'=>$business_name,
            'sender_name'=>$name,
            'sender_type'=>$customer_type,
            'message'=>$message,
            'message_type'=>$message_type,
            'group'=>$group,
            'status'=>$status);

        $this->db->insert('sms_received',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }


    function save_sms($msisdn,$recipient,$msg,$userid){
        $data =  array(
            'sent_to'=>$msisdn,
            'recipient'=>$recipient,
            'message'=>$msg,
            'sent_by'=>$userid);
        $this->db->insert('smsout',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }

    function save_bulksms($msisdn,$recipient,$msg,$userid){
        $data =  array(
            'sent_to'=>$msisdn,
            'recipient'=>$recipient,
            'message'=>$msg,
            'message_type'=>'BULK_SMS',
            'sent_by'=>$userid);
        $this->db->insert('smsout',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }
//=================================UNUSED METHODS======================================================================================



    function get_received_sms(){
        $this -> db-> select('*');
        $this -> db -> from('sms_received');
        $this->db->order_by('id','desc');
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }



    function get_bulk_sms(){
        $this -> db-> select('id,sent_to, message,fname,surname,smsout.created as created');
        $this -> db -> from('smsout');
        $this->db->join('users','smsout.sent_by = users.user_id');
        $this->db->order_by("smsout.created", "desc");
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }




}