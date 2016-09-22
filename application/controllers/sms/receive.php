<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/5/14
 * Time: 9:18 AM
 */

class Receive extends CI_Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('groups_model');
        $this->load->model('sms_model');
        $this->load->model('contacts_model');
        $this->load->model('distributors_m');
        $this->load->model('products_m');
        $this->load->model('customers_m');
        $this->load->model('stockists_m');
        $this->load->model('blacklist_model');
        ini_set('error_log', 'sms-app-error.log');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    //Hsenid API
    function index(){
        $this->load->library('sms/SmsReceiver.php');
        $this->load->library('sms/SmsSender.php');
        $this->load->library('sms/log.php');

        try {

            $receiver = new SmsReceiver(); // Create the Receiver object

            $content = $receiver->getMessage(); // get the message content
            $address = $receiver->getAddress(); // get the sender's address
            $requestId = $receiver->getRequestID(); // get the request ID
            $applicationId = $receiver->getApplicationId(); // get application ID
            $encoding = $receiver->getEncoding(); // get the encoding value
            $version = $receiver->getVersion(); // get the version

            logFile("[ content=$content, address=$address, requestId=$requestId, applicationId=$applicationId, encoding=$encoding, version=$version ]");
            log_message("info","[ content=$content, address=$address, requestId=$requestId, applicationId=$applicationId, encoding=$encoding, version=$version ]");

            $responseMsg="";
            $keyword="";
            $msg_group="";
            $message="";
            $msisdn = substr($address,4);

            //check if number is in blacklist
            $blacklisted = $this->blacklist_model->check_contact($msisdn);

            if(!$blacklisted){

                //check if stockist mobile is registered and ACTIVE
                $customer = $this->customers_m->get_customer($msisdn);

                if(!empty($customer->id)) {
                    $customer_type = $customer->customer_type;

                    log_message('info','Customer Type '.$customer_type);
                    $split = explode(' ', $content);

                    if (sizeof($split) < 2) {
                        $responseMsg = "Invalid message format";
                    } else {
                        $keyword = strtolower($split[0]);//Keyword
                        $part2 = $split[1];//Distributor Code OR GROUP
                        //$part3 = $split[2];//Invoice Number OR GROUP Message
                        $minus = strlen((string)$keyword) +1;
                        $message = substr($content,$minus);

                        if($keyword=='sub'){
                            //Check if group exists
                            $group_exist = $this->groups_model->check_group_name(trim($part2));

                            if($group_exist){
                                //save received message for the group
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type,trim($message),'SUBSCRIPTION',trim($part2),"AUTO-REPLIED");
                                if($saved){
                                    log_message("info","Registration message logged to received messages");
                                    $responseMsg = $this->contacts_model->add_group_contacts(trim($part2),$msisdn);
                                }else{
                                    $responseMsg="System error. Please try again ";
                                    log_message("info",$responseMsg." Failed to save subscriber to contacts");
                                }
                            }else{
                                //group not found save received message
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type,trim($message),'SUBSCRIPTION','NONE',"AUTO-REPLIED");
                                if($saved){
                                    $responseMsg="Subscription failed. Group name doesnt exist ";
                                    log_message("info",$responseMsg);
                                }
                            }


                        }elseif($keyword =="unsub"){
                            $group_exist = $this->groups_model->check_group_name(trim($part2));

                            if($group_exist){
                                log_message("info","Un-subscription request from ".$msisdn);

                                //save received message
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type,$message,'UNSUBSCRIPTION',trim($part2),"AUTO-REPLIED");
                                if($saved){
                                    $responseMsg = $this->contacts_model->remove_group_contact($part2,$msisdn);
                                    log_message("info",$responseMsg);
                                }
                            }else{
                                //group not found save received message
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type,$message,'UNSUBSCRIPTION',"NONE","AUTO-REPLIED");
                                if($saved){
                                    $responseMsg="Un-subscription failed. Group name doesnt exist";
                                    log_message("info",$responseMsg);
                                }
                            }

                        }elseif($keyword =="report"){
                            if($customer_type==="STOCKIST"){
                                //Check if distributor code exists
                                $dist_code = trim(strtoupper($part2));
                                $dist_code_exists = $this->distributors_m->check_distributor_code($dist_code);
                                if($dist_code_exists){
                                    //Check if there are products in message
                                    $product_separator = '*';
                                    $product_separator_position = strpos($content,$product_separator);
                                    if($product_separator_position === false){
                                        //Product separator non-existent
                                        log_message('info','Product Separator "*" NOT found in Message: "'.$content. '". Message From: "'.$msisdn.'"');
                                        $responseMsg = 'Wrong Format. Sms '.strtoupper($keyword).'" DISTRIBUTOR INVOICE*SKU1=QUANTITY*SKU2=QUANTITY". Example: Report D001 INV05 *sku1=50*sku2=265';
                                    }else{
                                        //Split products
                                        $split_products_section = explode('*', $content);
                                        $products_split_size =sizeof($split_products_section);
                                        if ($products_split_size < 2) {
                                            //No product included before or after product splitter
                                            log_message("info", "No products specified");
                                            $responseMsg = 'Wrong Format. Sms '.strtoupper($keyword).'" DISTRIBUTOR INVOICE*SKU1=QUANTITY*SKU2=QUANTITY". Example: Twiga D001 INV05 *sku1=50*sku2=265';
                                        }else{
                                            //Products included in message
                                            $keyword_distcode_invoice = $split_products_section[0];
                                            $minus_kd = strlen((string)$keyword) + strlen((string)$part2)+2;
                                            $invoice = substr($keyword_distcode_invoice,$minus_kd);

                                            //Check invoice is not empty
                                            if(trim($invoice)==""){
                                                //No invoice specified
                                                log_message("info", "No Invoice specified");
                                                $responseMsg = 'Wrong Format. Sms '.strtoupper($keyword).'" DISTRIBUTOR INVOICE*SKU1=QUANTITY*SKU2=QUANTITY". Example: Twiga D001 INV05 *sku1=50*sku2=265';
                                            }else{
                                                // Check sku code and amount for all splits of split_products

                                                $purchased_products=array();
                                                $products_valid = true;
                                                for( $i=1; $i<$products_split_size; $i++){
                                                    //Check if product-quantity divider exists

                                                    $product_quantity_separator = '=';
                                                    $product_quantity_separator_position = strpos($split_products_section[$i],$product_quantity_separator);

                                                    /* ToDo Check for multiple occurance of $product_quantity_separator in a single $split_products_section*/

                                                    if($product_quantity_separator_position === false){
                                                        // product-quantity separator doesnt exist
                                                        log_message('info','SKU - Quantity Separator "=" NOT found in Message: "'.$content. '". Message From: "'.$msisdn.'"');
                                                        $products_valid= false;
                                                        break;
                                                    }else{
                                                        // product-quantity separator found. Split products part to sku code & quantity
                                                        $split_sku_qty = explode('=',$split_products_section[$i]);

                                                        //verify if sku code exists
                                                        /* call verify method for sku code*/
                                                        $sku_code_exists = $this->products_m->check_sku_code(trim($split_sku_qty[0]));

                                                        if($sku_code_exists){
                                                            /*check if quantity is numeric*/
                                                            $quantity = trim($split_sku_qty[1]);
                                                            if (!ctype_digit($quantity)) {
                                                                //Quantity not a number value
                                                                log_message('info','Product '.$i.' with sku-code: "'.$split_sku_qty[0]. '" and quantity: "'.$split_sku_qty[1]. '", DOES NOT have a valid value for quantity. Message From: "'.$msisdn.'"');
                                                                $products_valid= false;
                                                                break;
                                                            }else{
                                                                //valid quantity value. Add code and qty to array
                                                                $purchased_products[$i]=array($split_sku_qty[0],$split_sku_qty[1]);
                                                            }
                                                        }else{
                                                            log_message('info','Product '.$i.' with sku-code: "'.$split_sku_qty[0]. '", DOES NOT match existing SKUs. Message From: "'.$msisdn.'"');
                                                            $products_valid= false;
                                                            break;
                                                        }

                                                    }
                                                }
                                                log_message('info','products_valid value:'.$products_valid);

                                                /* check if $products_valid is true or false*/
                                                if($products_valid){
                                                    //Save purchase report if all products are valid

                                                    $saved_id = $this->sms_model->save_purchase_report($customer->id,$msisdn,$dist_code,$invoice);

                                                    if($saved_id){
                                                        //save products in products table
                                                        $all_products_saved=true;
                                                        foreach($purchased_products as $purchase_prd){
                                                            log_message('info','sku code: '.$purchase_prd[0].', Quantity: '.$purchase_prd[1]);
                                                            $prd_sku_code=$purchase_prd[0];
                                                            $prd_qty=$purchase_prd[1];

                                                            /*todo optional: check before save if product for that invoice already exists*/
                                                            // $prd_exists = $this->sms_model->check_product_added($invoice,$prd_sku_code);

                                                            /*Save product*/
                                                            $prd_saved = $this->sms_model->save_purchase_products($saved_id,strtoupper($invoice),trim($prd_sku_code),trim($prd_qty));

                                                            if(!$prd_saved){
                                                                $all_products_saved=false;
                                                                log_message('info','Product not saved. Mobile: '.$msisdn.', Distributor Code: '.$dist_code.', Invoice No: '.$invoice.', SKU code: '.$prd_sku_code.', Quantity: '.$prd_qty);

                                                            }
                                                        }
                                                        if($all_products_saved){
                                                            $responseMsg = 'Your purchase report has being received successfully. Thank you';
                                                        }else{
                                                            $responseMsg = 'Your report has being received. Thank you';
                                                        }

                                                    }else{
                                                        log_message('info','Purchase Report Not saved. Check logs & database connection ');
                                                        $responseMsg = 'Report not recorded. Please try again';
                                                    }

                                                }else{
                                                    $responseMsg = 'Wrong Format. Sms "TWIGA DISTRIBUTOR INVOICE*SKU1=QUANTITY*SKU2=QUANTITY". Example: Twiga D001 INV05 *sku1=50*sku2=265';
                                                }

                                            }

                                        }
                                    }

                                }else{
                                    //Wrong format used save received message
                                    $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type,$message,"REPORT","NONE","AUTO-REPLIED");

                                    //Distributor code doesn't exist. Group doesnt Exist
                                    log_message("info", 'Neither Distributor Code nor Group exists in message: "' . $content . '" From: "' . $msisdn . '"');
                                    $responseMsg = "Invalid Distributor code or Non-existent Group. Try again using correct Distributor Code or existing Group name";

                                }


                            }else{
                                log_message("info","SMS received not from stockist: ".$msisdn." Message: ".$content);
                                $responseMsg = "You are not allowed to use this SMS service. Kindly contact Twiga Chemicals";
                            }

                        }elseif($keyword =="twiga"){
                            //Check if group exists
                            $group_exist = $this->groups_model->check_group_name(trim($part2));

                            if ($group_exist){
                                //Message is for Groups processing. save received message for the group
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type, $message, "GROUP",$part2, "PENDING");
                                if ($saved) {
                                    log_message("info", 'Group Message: "' . $content . '" From: "' . $msisdn . '"., Stockist');
                                    $responseMsg = "Your message has been received. Kindly wait for a reply. Thank you";
                                } else {
                                    $responseMsg = "System Error, Please try again";
                                    log_message("info", $responseMsg);
                                }
                            } else{
                                $saved = $this->sms_model->save_received_sms($customer->id,$msisdn,$customer->business_name, $customer->name,$customer->customer_type, $message, "GROUP",$customer_type, "PENDING");
                                if ($saved) {
                                    log_message("info", 'Group Message: "' . $content . '" From: "' . $msisdn . '"., Stockist');
                                    $responseMsg = "Your message has been received. Kindly wait for a reply. Thank you";
                                } else {
                                    $responseMsg = "System Error, Please try again";
                                    log_message("info", $responseMsg);
                                }
                            }

                        }
                    }


                }else{
                    log_message("info","SMS received from unregistered subscriber: ".$msisdn." Message: ".$content);
                    $responseMsg = "You are not allowed to use this SMS service. Kindly contact Twiga Chemicals using 254203942000";
                }
            }else{
                log_message("info","SMS received from blacklisted number: ".$msisdn." Message: ".$content);
                $responseMsg = "You are not allowed to use this SMS service. Kindly contact Twiga Chemicals using 254203942000";
            }

            log_message("info","Final SMS response to send: ".$responseMsg);



            //sending a one message
            $this->SendSMS(array($address),$responseMsg);



        } catch (Exception $ex) {
            //throws when failed sending or receiving the sms
            log_message("info","Error Code: ".$ex->getCode()." Error Message;".$ex->getMessage()." Line".$ex->getLine());
        }
    }

    function SendSMS($address,$message){
        try{
            // Create the sender object server url
            $sender = new SmsSender();

            $applicationId = "APP_000072";
            $encoding = "0";
            $version =  "1.0";
            $password = "72eacff6c2d1d3f3c72072a06cc69549";
            $sourceAddress = "20359";
            $deliveryStatusRequest = "0";
            $charging_amount = ":15.75";
            $destinationAddresses = $address;
            $binary_header = "";

            $res = $sender->sms($message, $destinationAddresses, $password, $applicationId, $sourceAddress, $deliveryStatusRequest, $charging_amount, $encoding, $version, $binary_header);
            log_message("info","SDP Response: ".$res);
        } catch (SmsException $ex) {
            //throws when failed sending or receiving the sms
            log_message("info","Error Code: ".$ex->getStatusCode());
            log_message("error","Error Message: ".$ex->getStatusMessage());
            error_log("ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}");
        }

    }

}