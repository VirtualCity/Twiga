<body >
<!--Header Section-->
<?Php $this->load->view('templates/app_header');?>

<!--Navigation Section-->
<?Php
if( $user_role === 'ADMIN'){
    $this->load->view('templates/navigation');
}else if($user_role === 'SUPER_USER'){
    $this->load->view('templates/navigation_super_user');
}else{
    $this->load->view('templates/navigation_user');
}
?>

<div id="content" class="no-sidebar"> <!-- Content start -->

    <div class="top_bar">
        <ul class="breadcrumb">
            <li><a href="<?=base_url('dashboard')?>"><i class="icon-home"></i> Home</a> <span class="divider">/</span></li>
            <li class="active"><a>Detailed Purchases Report</a></li>
        </ul>
    </div>
    <div class="inner_content">
        <div id="alert_placeholder">
            <?php
            $appmsg = $this->session->flashdata('appmsg');
            if(!empty($appmsg)){ ?>
                <div id="alertdiv" class="alert <?=$this->session->flashdata('alert_type') ?> "><a class="close" data-dismiss="alert">x</a><span><?= $appmsg ?></span></div>
            <?php } ?>
        </div>
        <div class="widgets_area">
            <div class="well blue">
                <div class="well-header">
                    <h5>View Detailed Purchases</h5>
                </div>
                <div class="well-content no_search">

                    <table class="table-bordered table-hover display responsive nowrap" width="100%" cellspacing="0" id="example">
                        <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>SKU Code</th>
                            <th>Quantity</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Unit(s)</th>
                            <th>Stockist Mobile</th>
                            <th>Business Name</th>
                            <th>Distributor Code</th>
                            <th>Distributor Name</th>
                            <th>Region</th>
                            <th>Town</th>
                            <th>Receive Date</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="<?= base_url('assets/js/jquery-1.11.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/tabletools/js/datatables.tableTools.js'); ?>"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#example').dataTable({
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "<?=base_url('reports/detailed_purchases/datatable')?>",
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[50, 100,200,500,1000], [50, 100,200,500,1000]],
            "dom": 'T<"clear">lfrtip',
            "scrollX": true,
			"scrollY": 400,
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>"
            },
            aoColumns: [

                { "mData": "invoice_no","bSearchable": true,"bSortable": true },
                { "mData": "sku_code","bSearchable": true,"bSortable": true },
                { "mData": "quantity","bSearchable": true,"bSortable": true },
                { "mData": "item_code","bSearchable": true,"bSortable": true },
                { "mData": "description","bSearchable": true,"bSortable": true },
                { "mData": "item_um","bSearchable": true,"bSortable": true },
                { "mData": "msisdn","bSearchable": true,"bSortable": true },
                { "mData": "business_name","bSearchable": true,"bSortable": true },
                { "mData": "distributor_code","bSearchable": true,"bSortable": true },
                { "mData": "distributor_name","bSearchable": true,"bSortable": true},
                { "mData": "region","bSearchable": true,"bSortable": true },
                { "mData": "town","bSearchable": true,"bSortable": true },
                { "mData": "created","bSearchable": true,"bSortable": true}

            ],
            "order": [[ 12, "desc" ]],
            "oLanguage": {
                "sProcessing": "<img src='<?= base_url('assets/img/loading.gif'); ?>'>"
            },
            fnInitComplete : function () {
                //oTable.fnAdjustColumnSizing();
            },
            fnServerData : function (sSource, aoData, fnCallback) {
                jQuery.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    });

</script>

<script src="<?php echo base_url('assets/js/jquery-ui-1.10.3.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap.js'); ?>"></script>

<script src="<?php echo  base_url('assets/js/library/jquery.collapsible.min.js'); ?>"></script>
<script src="<?php echo  base_url('assets/js/library/jquery.mCustomScrollbar.min.js'); ?>"></script>
<script src="<?php echo  base_url('assets/js/library/jquery.mousewheel.min.js'); ?>"></script>
<script src="<?php echo  base_url('assets/js/library/jquery.uniform.min.js'); ?>"></script>

<script src="<?php echo  base_url('assets/js/library/jquery.autosize-min.js'); ?>"></script>


<script src="<?php echo base_url('assets/js/design_core.js'); ?>"></script>

</body>
</html>
