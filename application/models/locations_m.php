<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/1/14
 * Time: 1:07 AM
 */

class Locations_m extends CI_Model{
    function get_all_locations(){
        $this->db->select('*');
        $this->db->from('locations');
        $query = $this -> db -> get();
        if ($query->num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }

    function get_location($id){
        $this->db->select('*');
        $this->db->from('locations');
        $this->db->where('id',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> row();
        }else{
            return false;
        }
    }

    function verify_location($id,$name){
        $this->db->select('*');
        $this->db->from('locations');
        $this->db->where('id !=',$id);
        $this->db->where('name',$name);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function add_location($location,$description){
        $data =  array(
            'name'=>ucwords($location),
            'description'=>$description
        );
        $this->db->set('created','NOW()',false);
        $this->db->insert('locations',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }

    function update_location($id,$location,$description){

        $data =  array(
            'name'=>ucwords($location),
            'description'=>$description
        );
        $this->db->where('id', $id);
        $query= $this->db->update('locations',$data);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    function get_all_location_Managers(){
        $this->db->select('*');
        $this->db->from('location_managers');
        $query = $this -> db -> get();
        if ($query->num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }
    }

    function get_location_manager($id){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('id',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> row();
        }else{
            return false;
        }
    }

    function check_location_manager_exists($id){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('location_id',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function verify_location_manager($id){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('manager_id',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function verify_location_manager_edit($id,$fname,$sname,$oname){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('fname',$fname);
        $this->db->where('sname',$sname);
        $this->db->where('oname',$oname);
        $this->db->where('id !=',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }


    function add_location_manager($location_id,$manager_id){
        $data =  array(
            'location_id'=>$location_id,
            'manager_id'=>$manager_id
        );
        $this->db->set('created', 'NOW()', FALSE);
        $this->db->insert('location_managers',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }
        return false;
    }
    function edit_location_manager($id,$fname,$sname,$oname,$email,$mobile,$location_id){
        $data =  array(
            'fname'=>$fname,
            'sname'=>$sname,
            'oname'=>$oname,
            'email'=>$email,
            'mobile'=>$mobile,
            'location_id'=>$location_id,
            'status'=>'active'
        );
        $this->db->where('id', $id);
        $query= $this->db->update('location_managers',$data);
        if($query){
            return true;
        }else{
            return false;
        }
    }



    function check_location_manager_email($id,$email){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('email',$email);
        $this->db->where('id !=',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function check_location_manager_mobile($id,$mobile){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('mobile',$mobile);
        $this->db->where('id !=',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function check_location_manager_location($id,$location_id){
        $this->db->select('*');
        $this->db->from('location_managers');
        $this->db->where('location_id',$location_id);
        $this->db->where('id !=',$id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }


}