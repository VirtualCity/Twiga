<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 10:12 AM
 */

class Products_m extends CI_Model{

    function add_product($sku_code,$item_code,$description,$item_um){

        $data =  array(
            'sku_code'=>$sku_code,
            'item_code'=>$item_code,
            'description'=>$description,
            'item_um'=>$item_um
        );
        $this->db->set('created', 'NOW()', FALSE);
        $this->db->insert('products',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }

    /*Retrieve a product with specified id*/
    function get_product($id){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('id',$id);
        $query = $this -> db -> get();

        if($query -> num_rows() === 1){
            return $query -> row();
        }else{
            return false;
        }
    }

    /*Verify sku_code doesnt exists apart from current product being edited*/
    function verify_sku_code($id,$sku_code){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('id !=',$id);
        $this->db->where('sku_code',$sku_code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /* Check if item_code exists*/
    function check_item_code($item_code){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('item_code',$item_code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /* Check if item_code exists*/
    function check_description($description){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('description',$description);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /* Check if sku_code exists*/
    function check_sku_code($sku_code){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('sku_code',$sku_code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Verify product sku_code doesnt exists apart from current product being edited*/
    function verify_product_description($id,$description){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('id !=',$id);
        $this->db->where('description',$description);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    //Verify item code if it exists other than edited record
    function verify_item_code($id,$item_code){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('id !=',$id);
        $this->db->where('item_code',$item_code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function update_product($id,$sku_code,$item_code,$description,$item_um){
        $data = array(
            'sku_code'=>$sku_code,
            'item_code'=>$item_code,
            'description'=>$description,
            'item_um'=>$item_um
        );
        $this->db->where('id', $id);
        $query = $this->db->update('products', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }
}