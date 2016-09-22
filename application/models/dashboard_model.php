<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 7/31/14
 * Time: 2:14 PM
 */

class Dashboard_model extends CI_Model{

    function get_todays_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('purchase_reports');
        $where = "DATE(created)= CURDATE()";
        $this -> db -> where($where);
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_weeks_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('purchase_reports');
        $where = "created >=( DATE(NOW()) - INTERVAL 7 DAY + INTERVAL 0 SECOND )";
        $this -> db -> where($where);
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_months_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('purchase_reports');
        $where = "created >=( DATE(NOW()) - INTERVAL 30 DAY + INTERVAL 0 SECOND )";
        $this -> db -> where($where);
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_stockists_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('customers');
        $this -> db -> where('customer_type','STOCKIST');
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_distributors_total(){
        $this -> db-> select('*');
        $this -> db -> from('customers');
        $this -> db -> where('customer_type','DISTRIBUTOR');
        $result = $this -> db -> get();
        $row_count = $result-> num_rows();
        return $row_count;
    }

    function get_farmers_total(){
        $this -> db-> select('*');
        $this -> db -> from('customers');
        $this -> db -> where('customer_type','FARMER');
        $result = $this -> db -> get();
        $row_count = $result-> num_rows();
        return $row_count;
    }

    function get_products_total(){
        $this -> db-> select('*');
        $this -> db -> from('products');
        $result = $this -> db -> get();
        $row_count = $result-> num_rows();
        return $row_count;
    }

    function get_contact_groups_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('groups');
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_outbox_total(){
        $this -> db-> select('count(*) AS count');
        $this -> db -> from('smsout');
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function get_blacklist_total(){
        $this -> db-> select('count(msisdn) AS count');
        $this -> db -> from('blacklist');
        $result = $this -> db -> get();

        if($result->num_rows()>0){
            $row = $result->row();

            $count = $row->count;

            return $count;

        }else{
            return 0;
        }
    }

    function getLastDaysSubscriptions($days){
        $this -> db-> select('*');
        $this -> db -> from('smsin');
        $where = "DATE(created)= CURDATE() - INTERVAL ".$days." DAY + INTERVAL 0 SECOND";
        $this -> db -> WHERE($where);
        $this-> db-> where('type','subscription');
        $this-> db-> where('recipient !=','none');
        $query = $this -> db -> get();
        $row_count = $query-> num_rows();

        return $row_count;


    }

    function getLastDaysUnsubscriptions($days){
        $this -> db-> select('*');
        $this -> db -> from('smsin');
        $where = "DATE(created)= CURDATE() - INTERVAL ".$days." DAY + INTERVAL 0 SECOND";
        $this -> db -> WHERE($where);
        $this-> db-> where('type','unsubscription');
        $this-> db-> where('recipient !=','none');
        $query = $this -> db -> get();
        $row_count = $query-> num_rows();

        return $row_count;


    }

    function getLastDaysGroupQueryRecievedMessages($days){
        $this -> db-> select('*');
        $this -> db -> from('smsin');
        $where = "DATE(created)= CURDATE() - INTERVAL ".$days." DAY + INTERVAL 0 SECOND";
        $this -> db -> WHERE($where);
        $this-> db-> where('type','group');
        $query = $this -> db -> get();
        $row_count = $query-> num_rows();

        return $row_count;

    }
    function getLastDaysGroupQueryRepliedMessages($days){
        $this -> db-> select('*');
        $this -> db -> from('smsin');
        $where = "DATE(created)= CURDATE() - INTERVAL ".$days." DAY + INTERVAL 0 SECOND";
        $this -> db -> WHERE($where);
        $this-> db-> where('type','group');
        $this-> db-> where('status','Replied');
        $query = $this -> db -> get();
        $row_count = $query-> num_rows();

        return $row_count;

    }

    function getLastDaysGroupQueryPendingMessages($days){
        $this -> db-> select('*');
        $this -> db -> from('smsin');
        $where = "DATE(created)= CURDATE() - INTERVAL ".$days." DAY + INTERVAL 0 SECOND";
        $this -> db -> WHERE($where);
        $this-> db-> where('type','group');
        $this-> db-> where('status','Pending');
        $query = $this -> db -> get();
        $row_count = $query-> num_rows();

        return $row_count;

    }
}