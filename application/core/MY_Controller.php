<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 9/16/14
 * Time: 10:03 AM
 */

class MY_Controller extends CI_Controller{
    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('logged_in')){
            redirect('login');
        }




    }
}