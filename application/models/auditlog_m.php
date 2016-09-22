<?php

/**
 * Created by PhpStorm.
 * User: blulalire
 * Date: 9/14/2016
 * Time: 2:50 PM
 */
class Auditlog_m extends CI_Model
{
    function log_import($type,$description,$content){

        $data =  array(
            'type'=>$type,
            'description'=>$description,
            'content'=>$content);

        $this->db->insert('import_logs',$data);
        $num_insert = $this->db->affected_rows();
        if($num_insert>0)
            return true;
        return false;
    }
}