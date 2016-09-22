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
            <li><a href="<?=base_url("towns")?>">Towns</a><span class="divider">/</span></li>
            <li class="active"><a>Technical Assistant's</a></li>
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

            <div class="well green">
                <div class="well-content no_search">
                    <table class="table table-striped table-hover">
                        <thead><h4>Region & Town</h4></thead>
                        <tbody>
                            <tr><td><strong>Region:</strong></td><td><a class="txt_blue"><?= $region_name;?></a></td></tr>
                            <tr><td><strong>Town:</strong></td><td><a class="txt_blue"><?= $town_name;?></a></td></tr>
                        </tbody>
                    </table>

                    <table class="table table-striped table-hover">
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>


            <div class="well blue">
                <div class="well-header">
                    <h5>Towns Technical Assistants</h5>
                </div>
                <div class="well-content no_search">

                    <table class="table-bordered table-hover display responsive nowrap" width="100%" cellspacing="0" id="example">
                        <thead>
                        <tr>
                            <th>TA Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Division</th>
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


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="<?= base_url('assets/js/jquery-1.11.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/tabletools/js/datatables.tableTools.js'); ?>"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#example').dataTable({
            "responsive": true,
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "<?=base_url('towns/towns_tas/'.$id)?>",
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50,100], [10, 20, 50,100]],
            "dom": 'T<"clear">lfrtip',
            "scrollX": true,
            "tableTools": {
                "sSwfPath": "<?= base_url('assets/tabletools/swf/copy_csv_xls_pdf.swf');?>"
            },
            aoColumns: [
                { "mData": "name","bSearchable": true,"bSortable": true },
                { "mData": "mobile","bSearchable": true,"bSortable": true },
                { "mData": "email","bSearchable": true,"bSortable": true },
                { "mData": "division","bSearchable": true,"bSortable": true },
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
