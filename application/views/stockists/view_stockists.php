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
            <li class="active"><a>Stockists</a></li>
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
                    <h5>View Stockists</h5>
                </div>
                <div class="well-content no_search no_padding">
                    <div class="navbar-inner">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#left-tab1" data-toggle="tab">Active Stockists</a></li>
                            <li><a href="#left-tab2" data-toggle="tab">Suspended Stockists</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="left-tab1">
                                <table class="table table-striped table-bordered table-hover datatable"  id="example">
                                    <thead>
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Stockist Name</th>
                                        <th>Mobile #1</th>
                                        <th>Mobile #2</th>
                                        <th>Email</th>
                                        <th>Town</th>
                                        <th>Region</th>
                                        <th>Last Modified</th>
                                        <th>Date Created</th>
                                        <?Php if($user_role!=="USER"){ ?>
                                            <th>Action</th>
                                        <?Php  } ?>

                                    </tr>
                                    </thead>

                                </table>
                        </div>
                        <div class="tab-pane" id="left-tab2">
                            <table class="table table-striped table-bordered table-hover datatable" id="example2">
                                <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Stockist Name</th>
                                    <th>Mobile #1</th>
                                    <th>Mobile #2</th>
                                    <th>Email</th>
                                    <th>Town</th>
                                    <th>Region</th>
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
            "processing": true,
            "serverSide": true,
            "scrollCollapse": true,
            "autoWidth":true,
            "jQueryUI": true,
            "scrollX": "100%",
            "scrollY": "400px",
           /* "sScrollXInner": "100%",*/
            "pagingType": "full_numbers",
            "pageLength": 50,
            "lengthMenu": [[50, 100,250,500], [50, 100,250,500]],
            "dom": 'T<"clearfix"><"margin-b"lf<"clearfix">>trip',
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>",
                "aButtons": [ "copy", "csv","xls","pdf" ]
            },
            columns: [
                { "data": "business_name"},
                { "data": "name"},
                { "data": "mobile1"},
                { "data": "mobile2"},
                { "data": "email"},
                { "data": "town"},
                { "data": "region"},
                { "data": "modified"},
                { "data": "created"}
                <?Php if($user_role!=="USER"){ ?>
                ,
                { "data": "actions","orderable": false,"searchable": false }
                <?Php  } ?>
            ],
            "order": [[ 0, "asc" ]],
            "oLanguage": {
                "sProcessing": "<img src='<?= base_url('assets/img/loading.gif'); ?>'>"
            },
            "ajax":{
                "url": "<?=base_url('stockists/datatable_active')?>",
                "type": "POST"
            }

        });

        jQuery('#example2').dataTable({
            "processing": true,
            "serverSide": true,
            "scrollCollapse": true,
            "jQueryUI": true,
            "scrollX": true,
            "scrollY": 400,
            "pagingType": "full_numbers",
            "pageLength": 50,
            "lengthMenu": [[50, 100,250,500], [50, 100,250,500]],
            "dom": 'T<"clearfix"><"margin-b"lf<"clearfix">>trip',
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>",
                "aButtons": [ "copy", "csv","xls","pdf" ]
            },
            columns: [
                { "data": "business_name" },
                { "data": "name"},
                { "data": "mobile1"},
                { "data": "mobile2"},
                { "data": "email"},
                { "data": "town"},
                { "data": "region"},
                { "data": "modified"},
                { "data": "created"}
                <?Php if($user_role!=="USER"){ ?>
                ,
                { "data": "actions","searchable": false,"orderable": false }
                <?Php  } ?>
            ],
            "order": [[ 0, "asc" ]],
            "oLanguage": {
                "sProcessing": "<img src='<?= base_url('assets/img/loading.gif'); ?>'>"
            },
            "ajax":{
                "url": "<?=base_url('stockists/datatable_suspended')?>",
                "type": "POST"
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
