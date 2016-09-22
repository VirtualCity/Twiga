<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 10:12 AM
 */

class Farmers_m extends CI_Model{

    function add_farmer($name,$mobile,$email,$town_id,$region_id){
        $contact_id = getGUID();
        $data =  array(
            'name'=>$name,
            'town_id'=>$town_id,
            'region_id'=>$region_id,
            'contact_id'=>$contact_id,
            'customer_type'=>'FARMER',
            'status'=>'ACTIVE'
        );
        $this->db->set('created','NOW()',false);
        $this->db->insert('customers',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            $this->add_contacts($contact_id,$mobile,$email);
            $this->add_farmer_to_group($mobile,"Farmers");
            return true;
        }
        return false;
    }
    function add_contacts($guid, $mobile, $email){
        $data =  array(
            'id'=>$guid,
            'mobile1'=>$mobile,
            'email'=>$email
        );
        $this->db->set('created','NOW()',false);
        $this->db->insert('contacts_info',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }else{
            return false;
        }
    }
    function add_farmer_to_group($msisdn,$group_name){
        //check if group exists
        $group_data = $this->get_groupid($group_name);

        if($group_data){
            //group exists
            $group_id =$group_data->id;

            //Check if farmer exists for that group
            $contact_exists = $this->check_subscribed_contact($group_id,$msisdn);

            if(!$contact_exists){
                $data =  array(
                    'msisdn'=>$msisdn,
                    'groupid'=>$group_id
                );

                $this->db->insert('group_contacts',$data);
                $num_insert = $this->db->affected_rows();

                if($num_insert>0){
                    return true;
                }else{
                    return false;
                }

            }
        }else{
            //group doesnt exist
            $data =  array(
                'name'=>'Farmers',
                'description'=>'Group for all farmers'
            );
            $this->db->insert('groups',$data);
            $num_insert = $this->db->affected_rows();
            $id = $this->db->insert_id();

            if($num_insert>0){
                $data =  array(
                    'msisdn'=>$msisdn,
                    'groupid'=>$id
                );

                $this->db->insert('group_contacts',$data);
                $num_insert = $this->db->affected_rows();

                if($num_insert>0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }

    }

    //get id of group if it exists
    function get_groupid($group){
        $this->db->select('*');
        $this->db->from('groups');
        $this->db->where('name',$group);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> row();
        }else{
            return false;
        }
    }

    //check if contact is already in the group
    function check_subscribed_contact($groupid,$msisdn){
        $this->db->select('*');
        $this->db->from('group_contacts');
        $this->db->where('groupid',$groupid);
        $this->db->where('msisdn',$msisdn);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Retrieve a farmer with specified id*/
    function get_farmer($id){
        $this->db->select('customers.id AS id, name, town_id, region_id, contact_id, status, customers.modified, customers.created, mobile1, mobile2, email');
        $this->db->from('customers');
        $this->db->join('contacts_info','customers.contact_id = contacts_info.id');
        $this->db->where('customers.id',$id);
        $query = $this -> db -> get();

        if($query -> num_rows() === 1){
            return $query -> row();
        }else{
            return false;
        }
    }

    /*Verify farmer name doesnt exists apart from current being edited*/
    function verify_farmer_name($id,$name){
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('id !=',$id);
        $this->db->where('name',$name);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Verify farmer mobile doesnt exists apart from current farmer being edited*/
    function verify_farmer_mobile($contact_id,$mobile){
        $this->db->select('*');
        $this->db->from('contacts_info');
        $this->db->where('id !=',$contact_id);
        $this->db->where('mobile1',$mobile);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    //verify if mobile number belongs to registered farmer
    function check_mobile($mobile){
        $this->db->select('*');
        $this->db->from('contacts_info');
        $this->db->where('mobile1',$mobile);
        $this->db->or_where('mobile2',$mobile);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
           return true;
        }else{
            return false;
        }
    }

    function update_farmer($id,$name,$contact_id,$mobile,$email,$town_id,$region_id){
        $data = array(
            'name' => $name,
            'town_id'=>$town_id,
            'region_id'=>$region_id,
        );
        $this->db->where('id', $id);
        $query = $this->db->update('customers', $data);

        if($query){
            $query = $this->update_contact($contact_id,$mobile,$email);
            if($query){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function update_contact($contact_id,$mobile,$email){
        $data = array(
            'mobile1' => $mobile,
            'email'=>$email,
        );
        $this->db->where('id', $contact_id);
        $query = $this->db->update('contacts_info', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    function activate($id){
        $data = array('status' => 'ACTIVE');
        $this -> db -> where('id',$id);
        $query = $this->db->update('customers', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    function suspend($id){
        $data = array('status' => 'SUSPENDED');
        $this -> db -> where('id',$id);
        $query = $this->db->update('customers', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }
}