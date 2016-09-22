<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 10/6/2014
 * Time: 9:30 AM
 */

class Products extends Admin_Controller{

    function __construct(){
        parent::__construct();
        $this->load->helper('buttons_helper');
        $this->load->model('products_m');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index(){

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Products";
        $this->load->view('templates/header', $data);
        $this->load->view('products/view_products',$data);

    }

    function datatable(){
        $this->datatables->select('id,sku_code,item_code,description,item_um,modified,created')
            ->unset_column('id')
            ->add_column('actions', get_view_products_buttons('$1'), 'id')
            ->from('products');
        echo $this->datatables->generate();
    }

    function add(){
        // SET VALIDATION RULES
        $this->form_validation->set_rules('sku_code', 'SKU Code', 'required|max_length[50]|is_unique[products.sku_code]');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required|max_length[50]|is_unique[products.item_code]');
        $this->form_validation->set_rules('description', 'Description', 'required|max_length[100]|is_unique[products.description]');
        $this->form_validation->set_rules('item_um', 'Unit of Measure', 'required|max_length[30]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $sku_code ="";
        $item_code = "";
        $description ="";
        $item_um ="";

        // has the form been submitted
        if($this->input->post()){
            $sku_code = trim($this->input->post('sku_code'));
            $item_code = trim($this->input->post('item_code'));
            $description = trim($this->input->post('description'));
            $item_um = trim($this->input->post('item_um'));

            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){


                //Save new product
                $saved = $this->products_m->add_product($sku_code,$item_code,strtoupper($description),strtoupper($item_um));

                if($saved){
                    // Display success message
                    $this->session->set_flashdata('appmsg', 'New Product added successfully!');
                    $this->session->set_flashdata('alert_type', 'alert-success');
                    redirect('products/add');

                }else{
                    // Display fail message
                    $this->session->set_flashdata('appmsg', 'New Product NOT added! Check logs');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('products/add');
                }
            }
        }

        $data['sku_code']=$sku_code;
        $data['item_code']=$item_code;
        $data['description']=$description;
        $data['item_um']=$item_um;
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Add Product";
        $this->load->view('templates/header', $data);
        $this->load->view('products/add_product',$data);

    }

    function edit($id=null){
        if(!empty($id)){
            //retrieve the msisdn for the recipient
            $to_edit = $this->products_m->get_product($id);

            //display reply view
            $data['id']=$id;
            $data['sku_code']=$to_edit->sku_code;
            $data['item_code']=$to_edit->item_code;
            $data['description']=$to_edit->description;
            $data['item_um']=$to_edit->item_um;
        }else{
            //return fail. distributor code already in use
            $this->session->set_flashdata('appmsg', 'Error encountered! No identifier specified');
            $this->session->set_flashdata('alert_type', 'alert-warning');
            redirect('products');
        }

        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Edit Product";
        $this->load->view('templates/header', $data);
        $this->load->view('products/edit_product',$data);

    }

    function modify(){
        // SET VALIDATION RULES


        $this->form_validation->set_rules('sku_code', 'SKU Code', 'required|max_length[50]');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required|max_length[50]');
        $this->form_validation->set_rules('description', 'Description', 'required|max_length[100]');
        $this->form_validation->set_rules('item_um', 'Unit of Measure', 'required|max_length[30]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // has the form been submitted
        if($this->input->post()){
            $id = trim($this->input->post('id'));
            $sku_code = trim($this->input->post('sku_code'));
            $item_code = trim($this->input->post('item_code'));
            $description = trim($this->input->post('description'));
            $item_um = trim($this->input->post('item_um'));
            //Does it have valid form info (not empty values)
            if($this->form_validation->run()){

                //verify sku code if it exists other than modified record
                $sku_code_exists = $this->products_m->verify_sku_code($id,$sku_code);

                if($sku_code_exists){
                    //return fail. product code already in use
                    $this->session->set_flashdata('appmsg', 'This SKU Code "'.$sku_code.'" is already in assigned by a different product');
                    $this->session->set_flashdata('alert_type', 'alert-danger');
                    redirect('products/edit/'.$id);
                }else{
                    //SkU Code is unique to edited field

                    //verify if item code exists other than edited record
                    $item_code_exists = $this->products_m->verify_item_code($id,$item_code);

                    if($item_code_exists){
                        //return fail. product name already in use
                        $this->session->set_flashdata('appmsg', 'This Item Code "'.$item_code.'" is already in assigned by a different product');
                        $this->session->set_flashdata('alert_type', 'alert-danger');
                        redirect('products/edit/'.$id);
                    }else{
                        //verify name if it exists other than modified record
                        $description_exists = $this->products_m->verify_product_description($id,strtoupper($description));

                        if($description_exists){
                            //return fail. product name already in use
                            $this->session->set_flashdata('appmsg', 'This Product description "'.strtoupper($description).'" is already exists for a different product');
                            $this->session->set_flashdata('alert_type', 'alert-danger');
                            redirect('products/edit/'.$id);
                        }else{
                            //Save new product
                            $saved = $this->products_m->update_product($id,$sku_code,$item_code,strtoupper($description),strtoupper($item_um));

                            if($saved){
                                // Display success message
                                $this->session->set_flashdata('appmsg', 'Product modified successfully!');
                                $this->session->set_flashdata('alert_type', 'alert-success');
                                redirect('products');

                            }else{
                                // Display fail message
                                $this->session->set_flashdata('appmsg', 'Product NOT modified! Check logs');
                                $this->session->set_flashdata('alert_type', 'alert-danger');
                                redirect('products');
                            }

                        }
                    }

                }

            }
            $errors = validation_errors();
            $this->session->set_flashdata('appmsg', $errors);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('products/edit/'.$id);
        }

        redirect('products');
    }

    function import(){

        $data['base']=$this->config->item('base_url');
        $data['user_role'] = $this->session->userdata('role');
        $data['title'] = "Import Products";
        $this->load->view('templates/header', $data);
        $this->load->view('products/import_products',$data);
    }

    function do_upload(){
        $config['upload_path'] = './uploads/products/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['overwrite'] = TRUE;
        $config['max_size']	= '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $error = array('error' => $this->upload->display_errors());

            log_message('error','Error: File not imported. '.$error);
            // Display fail message
            $this->session->set_flashdata('appmsg', $error['error']);
            $this->session->set_flashdata('alert_type', 'alert-danger');
            redirect('products/import');
        }else{
            $data = array('upload_data' => $this->upload->data());

            foreach($data['upload_data'] as $item => $value){
                log_message('info','item: '.$item. ' value: '.$value);
            }

            $data2 =  $this->upload->data();
            $file_name= $data2['file_name'];

            $result = $this->import_excel($file_name);

            $importedNo =$result['count'];
            $existing_regions = $result['existing'];
            $notImported = $result['notadded'];

            // Display success message
            $this->session->set_flashdata('existing', $existing_regions);
            $this->session->set_flashdata('notimported', $notImported);
            $this->session->set_flashdata('appmsg', 'Products imported: '.$importedNo);
            $this->session->set_flashdata('alert_type', 'alert-success');
            redirect('products/import');
        }
    }

    function import_excel($fileName){
        $this->load->library('Excel');
        //  Include PHPExcel_IOFactory


        $inputFileName = './uploads/products/'.$fileName;

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'D';

        $addCounter = 0;
        $notAdded = '';
        $existingItemCodes = '';
        $existingDescriptions = '';
        $existingSkus = '';


        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $productData = $rowData[0];
            $productCode = trim($productData[0]);
            $productDescription = trim($productData[1]);
            $productUM = trim($productData[2]);
            $productSKU = trim($productData[3]);

            //Check if all fields are not null
            if($productCode!=null AND $productDescription != null AND $productUM != null AND $productSKU != null){

                //Check if item code already exists
                $itemCodeExists = $this->products_m->check_item_code($productCode);
                if($itemCodeExists){
                    $existingItemCodes =$existingItemCodes.' | '.$productCode;
                    //log the region as not added
                    log_message('info',$productCode.' EXISTS! ');
                }else{
                    //Check if item code already exists
                    $descriptionExists = $this->products_m->check_description($productDescription);
                    if($descriptionExists){
                        $existingDescriptions =$existingDescriptions.' | '.$productDescription;
                        //log the region as not added
                        log_message('info',$productDescription.' EXISTS! ');
                    }else{
                        //Check if item code already exists
                        $skuExists = $this->products_m->check_sku_code($productSKU);
                        if($skuExists){
                            $existingSkus =$existingSkus.' | '.$productSKU;
                            //log the region as not added
                            log_message('info',$productSKU.' EXISTS! ');
                        }else{
                            //add the region add_product($sku_code,$item_code,$description,$item_um)
                            $product_added = $this->products_m->add_product($productSKU,$productCode,strtoupper($productDescription),strtoupper($productUM));

                            if($product_added){
                                $addCounter++;
                                //log the region added
                                log_message('info',$productCode.' '.$productDescription.' '.$productUM.' '.$productSKU.' ADDED SUCCESSFULLY! ');

                            }else{
                                $notAdded = $notAdded. ' | '.$productDescription;
                                //log the region as not added
                                log_message('info','PRODUCT NOT ADDED! '.$productCode.' '.$productDescription.' '.$productUM.' '.$productSKU);
                            }
                        }

                    }

                }
            }

        }

        $existingVariables ="";
        if(!empty($existingItemCodes)){
            $existingVariables = 'Item Codes: ('.$existingItemCodes.') ';
        }

        if(!empty($existingDescriptions)){
            $existingVariables =$existingVariables .'Descriptions: ('.$existingDescriptions.') ';
        }

        if(!empty($existingSkus)){
            $existingVariables =$existingVariables .'SKUs: ('.$existingSkus.') ';
        }


        $import_result = array('count'=>$addCounter,'existing'=>$existingVariables,'notadded'=>$notAdded);

        return $import_result;
    }


}