<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/1/14
 * Time: 1:53 AM
 */

class Contacts_model extends CI_Model{
    function get_contact($id){
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('id',$id);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> row();
        }else{
            return false;
        }
    }

    function get_all_contacts(){
        $query = $this->db->get('contacts');

        if ($query->num_rows() > 0){
            foreach($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function get_group_contacts($group){
        $this->db->select('msisdn');
        $this->db->from('group_contacts');
        $this->db->where('groupid',$group);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return $query -> result();
        }else{
            return false;
        }

    }

    function add_group_contacts($group,$msisdn){
        log_message("info","Adding to SMS groups");
        $groupid = $this->get_groupid($group);

        if($groupid){

            $id = $groupid->id;
            log_message("info","Group id found ".$id);

            //Check if user exists for that group
            $contact_exists = $this->check_subscribed_contact($id,$msisdn);

            if(!$contact_exists){
                $data =  array(
                    'msisdn'=>$msisdn,
                    'groupid'=>$id
                );

                $this->db->insert('group_contacts',$data);
                $num_insert = $this->db->affected_rows();
                if($num_insert>0){
                    $responseMsg='You have subscribed to "'.$group.'" SMS group. To unsubscribe SMS "unsub '.$group.'"  to 20359';
                    log_message("info","Registration for ".$msisdn." successful ");
                    return $responseMsg;
                }else{
                    $responseMsg="System error. Please try again ";
                    log_message("info",$responseMsg." Failed to add Subscriber to SMS groups");
                    return $responseMsg;
                }

            }else{
                $responseMsg="You are already subscribed to ".$group;
                log_message("info",$responseMsg." Subscriber already subscribed to groups");
                return $responseMsg;
            }


        }else{
            $responseMsg="SMS group Not found. ".$group;
            log_message("info",$responseMsg." SMS group Not found. Contact cant be added to group");
            return $responseMsg;
        }

    }

    function add_contact_togroup_viaId($msisdn,$group_id){
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

    function add_contacts($msisdn){
        log_message("info","Adding to contact groups");


        //Check if user exists for that group
        $contact_exists = $this->check_contact($msisdn);

        if(!$contact_exists){
            $data =  array(
                'msisdn'=>$msisdn
            );
            $this->db->set('created', 'NOW()', FALSE);
            $this->db->insert('contacts',$data);
            $num_insert = $this->db->affected_rows();
            if($num_insert>0){
                log_message("info","User added to contacts list");

            }else{
                log_message("info","User NOT added to contacts list");
            }

        }else{
            log_message("info"," User already exists in contacts list");
        }
    }

    function remove_group_contact($group,$msisdn){
        log_message("info","Remove from SMS groups");
        //get group id
        $groupid = $this->get_groupid($group);

        if($groupid){
            $id = $groupid->id;
            //Check if user exists for that group
            $contact_exists = $this->check_subscribed_contact($id,$msisdn);

            if($contact_exists){
                $query=$this->db->delete('group_contacts', array('msisdn' => $msisdn, 'groupid'=>$id));

                //$this->db->affected_rows();
                if($query){
                    return "You have successfully unsubscribed from ".$group;
                }else{
                    return "System error. Please try again ";
                }
            }else{
                return "You have not subscribed to ".$group;
            }
        }else{
            log_message("info","Error group: ".$group." not found while unsubscribing");
            return "Error group: ".$group." not found";
        }

    }

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

    function check_contact($msisdn){
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('msisdn',$msisdn);
        $query = $this -> db -> get();

        if($query -> num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function edit_contact($id,$msisdn,$name,$email,$address){
        $data = array(
            'msisdn' => $msisdn,
            'name' => $name,
            'email' => $email,
            'address' => $address,
        );
        $this->db->where('id', $id);
        $query = $this->db->update('contacts', $data);

        if($query){
            return true;
        }else{
            return false;
        }
    }
}