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
            <li><a href="<?=base_url("dashboard")?>"><i class="icon-home"></i> Home</a> <span class="divider">/</span></li>
            <li><a href="<?=base_url("towns")?>">Towns</a><span class="divider">/</span></li>
            <li class="active"><a >Edit Town</a></li>
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
                    <h5>Edit Town</h5>
                </div>
                <div class="well-content no_search">

                    <form action="<?=base_url('towns/modify')?>" method="post" class="form-horizontal">
                        <input type="hidden" name="id" value="<?=$id?>"/>
                        <div class="form_row">
                            <label for="town" class="field_name align_right lblBold">Town Name </label>
                            <div class="field">
                                <input type="text" name="town" id="town" placeholder="Town Name" class="span6" value="<?php echo $town;?>"/>
                                <font color="red"> *</font>
                                <div><font color="red"> <?php echo form_error('town'); ?> </font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="region_id" class="field_name align_right lblBold">Region</label>
                            <div class="field">
                                <select name="region_id" id="region_id" class="span6" >
                                    <option value="">---Please Select Region---</option>
                                    <?php
                                    if(!empty($regions)){
                                        foreach($regions as $row) { ?>
                                            <option value="<?=$row->id?>" <?php if ($row->id ===$region_id){echo "selected";}?>><?=$row->name?></option>
                                        <?php   }
                                    } ?>
                                </select> <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('region_id'); ?></font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label class="field_name align_right"></label>
                            <div class="field">
                                <button type="submit" class="btn btn-large blue"><i class="icon-plus"></i> Edit Town</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.3.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>

<script src="<?php echo base_url(); ?>assets/js/library/jquery.collapsible.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/library/jquery.mCustomScrollbar.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/library/jquery.mousewheel.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/library/jquery.uniform.min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/library/jquery.autosize-min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/design_core.js"></script>

<script>



    function showalert(message,alerttype){
        $('#alert_placeholder').append('<div id="alertdiv" class="alert ' + alerttype + '"><a class="close" data-dismiss="alert">x</a><span>'+message+'</span></div>')
        setTimeout(function() {
            $('#alertdiv').remove();
        },6000);
    }

    function showMessage(message){
        if(message.length>0){
            showalert2(message,"alert-info");
        }
    }

    function showalert2(message,alerttype){
        $('#alert_placeholder').append('<div id="alertdiv" class="alert ' + alerttype + '"><a class="close" data-dismiss="alert">x</a><span>'+message+'</span></div>')
        setTimeout(function() {
            $('#alertdiv').remove();
        },3000);


    }

</script>

</body>
</html>