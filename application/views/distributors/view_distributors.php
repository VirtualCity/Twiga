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
            <li class="active"><a>Distributors</a></li>
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
                    <h5>View Distributors</h5>
                </div>
                <div class="well-content no_search no_padding">
                    <div class="navbar-inner">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#left-tab1" data-toggle="tab">Active Distributors</a></li>
                            <li><a href="#left-tab2" data-toggle="tab">Inactive Distributors</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="left-tab1">
                                <table class="table table-striped table-bordered table-hover datatable"  id="example">
                                    <thead>
                                    <tr>
                                        <th>Distributor Code</th>
                                        <th>Mobile</th>
                                        <th>Distributor Name</th>
                                        <th>Contact Name</th>
                                        <th>Email</th>
                                        <th>Region</th>
                                        <th>Town</th>
                                        <th>Last Modified</th>
                                        <th>Date Created</th>
                                        <?Php if($user_role!=="USER"){ ?>
                                            <th>Action</th>
                                        <?Php  } ?>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>

                                </table>
                        </div>
                        <div class="tab-pane" id="left-tab2">
                            <table class="table table-striped table-bordered table-hover datatable"  id="example2">
                                <thead>
                                <tr>
                                    <th>Distributor Code</th>
                                    <th>Mobile</th>
                                    <th>Distributor Name</th>
                                    <th>Contact Name</th>
                                    <th>Email</th>
                                    <th>Region</th>
                                    <th>Town</th>
                                    <th>Last Modified</th>
                                    <th>Date Created</th>
                                    <?Php if($user_role!=="USER"){ ?>
                                        <th>Action</th>
                                    <?Php  } ?>

                                </tr>
                                </thead>

                            </table>
                        </div>

                    </div>
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
            responsive: true,
            "bServerSide": true,
            "sAjaxSource": "<?=base_url('distributors/datatable_active')?>",
            "bJQueryUI": true,
            "scrollX": true,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[50, 100,200,500], [50, 100,200,500]],
            "dom": 'T<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>"
            },
            aoColumns: [

                { "mData": "code","bSearchable": true,"bSortable": true },
                { "mData": "mobile1","bSearchable": true,"bSortable": true },
                { "mData": "business_name","bSearchable": true,"bSortable": true },
                { "mData": "name","bSearchable": true,"bSortable": true },
                { "mData": "email","bSearchable": true,"bSortable": true },
                { "mData": "region","bSearchable": true,"bSortable": true },
                { "mData": "town","bSearchable": true,"bSortable": true },
                { "mData": "modified","bSearchable": true,"bSortable": true},
                { "mData": "created","bSearchable": true,"bSortable": true}
                <?Php if($user_role!=="USER"){ ?>
                ,
                { "mData": "actions","bSearchable": false,"bSearchable": false }
                <?Php  } ?>
            ],
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

        jQuery('#example2').dataTable({
            "processing": true,
            "bServerSide": true,
            "responsive": true,
            "sAjaxSource": "<?=base_url('distributors/datatable_inactive')?>",
            "bJQueryUI": true,
            "scrollX": true,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[50, 100,200,500], [50, 100,200,500]],
            "dom": 'T<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>"
            },
            aoColumns: [

                { "mData": "code","bSearchable": true,"bSortable": true },
                { "mData": "mobile1","bSearchable": true,"bSortable": true },
                { "mData": "business_name","bSearchable": true,"bSortable": true },
                { "mData": "name","bSearchable": true,"bSortable": true },
                { "mData": "email","bSearchable": true,"bSortable": true },
                { "mData": "region","bSearchable": true,"bSortable": true },
                { "mData": "town","bSearchable": true,"bSortable": true },
                { "mData": "modified","bSearchable": true,"bSortable": true},
                { "mData": "created","bSearchable": true,"bSortable": true}
                <?Php if($user_role!=="USER"){ ?>
                ,
                { "mData": "actions","bSearchable": false,"bSearchable": false }
                <?Php  } ?>
            ],
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
<script src="<?php echo base_url('assets/datatables/js/responsive.js'); ?>"></script>
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
