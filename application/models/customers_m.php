<?php

/**
 * Created by PhpStorm.
 * User: blulalire
 * Date: 9/16/2016
 * Time: 5:54 PM
 */
class Customers_m extends CI_Model
{

	function get_customer($mobile){
		$this->db->select('customers.id AS id, business_name, name, town_id, region_id, contact_id, customer_type,status, customers.modified, customers.created, mobile1, mobile2, email');
		$this->db->from('customers');
		$this->db->join('contacts_info','customers.contact_id = contacts_info.id');
		$this->db->where('contacts_info.mobile1',$mobile);
		$this->db->or_where('contacts_info.mobile2',$mobile);
		$query = $this -> db -> get();

		if($query -> num_rows() > 0){
			return $query -> row();
		}else{
			return false;
		}

	}

    function get_distributor($id){
        $this->db->select('customers.id AS id, business_name,name, code, town_id, region_id, contact_id, status, customers.modified, customers.created, mobile1, mobile2, email');
        $this->db->from('customers');
        $this->db->join('contacts_info','customers.contact_id = contacts_info.id');
        $this->db->join('distributors_codes','customers.id = distributors_codes.customer_id');
        $this->db->where('customers.id',$id);
        $query = $this -> db -> get();

        if($query -> num_rows() === 1){
            return $query -> row();
        }else{
            return false;
        }
    }

}