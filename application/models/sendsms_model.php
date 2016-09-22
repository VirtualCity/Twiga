<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/6/14
 * Time: 11:25 AM
 */

class Sendsms_model extends CI_Model{

    function send_sms($destination,$msg){

       /* $this->load->library('sms/SmsSender.php');
        $this->load->library('sms/log.php');*/


        log_message('info','Message to send: '.$msg);

        try {
            $responseMsg=$msg;

            // Create the sender object server url
            $sender = new SmsSender();

            //sending a one message

            $applicationId = "APP_000072";
            $encoding = "0";
            $version =  "1.0";
            $password = "72eacff6c2d1d3f3c72072a06cc69549";
            $sourceAddress = "20359";
            $deliveryStatusRequest = "0";
            $charging_amount = "0";
            $destinationAddresses = $destination;
            $binary_header = "";
          //  log_message("info","Sending Parameters ".$responseMsg ." ". $destinationAddresses[0]." ". $password." ".$applicationId." ".$sourceAddress." ".$deliveryStatusRequest." ".$charging_amount." ".$encoding." ".$version." ".$binary_header);
            $res = $sender->sms($responseMsg, $destinationAddresses, $password, $applicationId, $sourceAddress, $deliveryStatusRequest, $charging_amount, $encoding, $version, $binary_header);

            $server_response = json_decode($res,true);
            if(is_array($server_response)){
                log_message("info","SDP response: ".$server_response['statusCode']);

                if($server_response['statusCode']=='S1000'){
                    return 'success';
                }
            }
            log_message("info","SDP response ".var_export($res,true));

        } catch (SmsException $ex) {
            //throws when failed sending or receiving the sms
            log_message("info","Status code error: ".$ex->getStatusCode());
            log_message("info","Status message error: ".$ex->getStatusMessage());
            error_log("ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}");
            return "fail";
        }

    }
}