<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 10:12 AM
 */

class Distributors_m extends CI_Model{

    function add_distributor($code,$biz_name,$name,$mobile,$email,$town_id,$region_id){

        $contact_id = getGUID();
        $data =  array(
            'business_name'=>$biz_name,
            'name'=>$name,
            'town_id'=>$town_id,
            'region_id'=>$region_id,
            'contact_id'=>$contact_id,
            'customer_type'=>'DISTRIBUTOR',
            'status'=>'ACTIVE'
        );
        $this->db->set('created','NOW()',false);
        $this->db->insert('customers',$data);
        $customer_id = $this->db->insert_id();
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            $this->add_distributor_code($customer_id,$code);
            $this->add_contacts($contact_id,$mobile,$email);
            $this->add_to_group($mobile,"Distributors");
            return true;
        }
        return false;
    }
    function add_contacts($guid, $mobile,$email){
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

    function add_to_group($msisdn,$group_name){
        //check if group exists
        $group_data = $this->get_groupid($group_name);

        if($group_data){
            //group exists
            $group_id =$group_data->id;

            //Check if stockist exists for that group
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
                'name'=>$group_name,
                'description'=>'Group for all '.$group_name
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

        $this->db->select('*');
        $this->db->from('groups');
        $this->db->where('name',$group_name);
        $query = $this->db->get();
        if($query -> num_rows() === 1){
            //Group exists. Add
        }else{

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
    function add_distributor_code($customer_id, $code){
        $data =  array(
            'customer_id'=>$customer_id,
            'code'=>$code
        );
        $this->db->insert('distributors_codes',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0){
            return true;
        }else{
            return false;
        }
    }

    /*Retrieve a distributor with specified id*/
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

    /*Verify distributor code doesnt exists apart from current distributor being edited*/
    function verify_distributor_code($id,$code){
        $this->db->select('*');
        $this->db->from('distributors_codes');
        $this->db->where('customer_id !=',$id);
        $this->db->where('code',$code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Check distributor code doesnt exists*/
    function check_distributor_code($code){
        $this->db->select('*');
        $this->db->from('distributors_codes');
        $this->db->where('code',$code);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Check distributor name doesnt exists*/
    function check_distributor_name($business_name){
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('business_name',$business_name);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Check distributor email doesnt exists*/
    function check_distributor_email($email){
        $this->db->select('*');
        $this->db->from('contacts_info');
        $this->db->where('email',$email);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Check distributor mobile doesnt exists*/
    function check_distributor_mobile($mobile){
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

    /*Verify distributor code doesnt exists apart from current distributor being edited*/
    function verify_distributor_bizname($id,$business_name){
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('id !=',$id);
        $this->db->where('business_name',$business_name);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Verify distributor mobile doesnt exists apart from current distributor being edited*/
    function verify_distributor_mobile($contact_id,$mobile){
        $this->db->select('*');
        $this->db->from('contacts_info');
        $this->db->where('id !=',$contact_id);
        $this->db->where('mobile1',$mobile);
        $this->db->or_where('mobile2',$mobile);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /*Verify distributor email doesnt exists apart from current distributor being edited*/
    function verify_distributor_email($contact_id,$email){
        $this->db->select('*');
        $this->db->from('contacts_info');
        $this->db->where('id !=',$contact_id);
        $this->db->where('email',$email);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function update_distributor($id,$code,$biz_name,$name,$contact_id,$mobile,$email,$town_id,$region_id,$status){
        $data = array(
            'business_name'=>$biz_name,
            'name' => $name,
            'town_id'=>$town_id,
            'region_id'=>$region_id,
            'status' => $status,
        );
        $this->db->where('id', $id);
        $query = $this->db->update('customers', $data);

        if($query){
            $this->update_distributr_code($id,$code);
            $this->update_contact($contact_id,$mobile,$email);
            return true;

        }else{
            return false;
        }
    }

    function update_distributr_code($customer_id,$code){
        $data =  array(
            'code'=>$code
        );
        $this->db->where('customer_id', $customer_id);
        $query = $this->db->update('distributors_codes',$data);

        if($query){
            return true;
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

    function activate_distributor($id){
        $data = array('status' => 'ACTIVE');
        $this -> db -> where('id',$id);
        $query = $this->db->update('customers', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    function deactivate_distributor($id){
        $data = array('status' => 'INACTIVE');
        $this -> db -> where('id',$id);
        $query = $this->db->update('customers', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }
}