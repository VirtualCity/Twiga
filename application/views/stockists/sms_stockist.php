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
            <li class="active"><a href="<?=base_url("stockists")?>">Stockists</a><span class="divider">/</span></li>
            <li class="active"><a>SMS</a></li>
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
                <div class="well green">
                    <div class="well-content no_search">
                        <table class="table table-striped table-hover">
                            <thead><h4>Stockist Details</h4></thead>
                            <tbody>
                            <tr><td><strong>Business Name:</strong></td><td><a class="txt_blue"><?= $biz_name;?></a></td></tr>
                            <tr><td><strong>Stockist Name:</strong></td><td><a class="txt_blue"><?= $stockist_name;?></a></td></tr>
                            <tr><td><strong>Mobile 1:</strong></td><td><a class="txt_blue"><?= $mobile1;?></a></td></tr>
                            <tr><td><strong> Mobile2:</strong></td><td><a class="txt_blue"><?= $mobile2;?></a></td></tr>
                            </tbody>
                        </table>

                        <table class="table table-striped table-hover">
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="well-header">
                    <h5>SMS Stockist</h5>
                </div>
                <div class="well-content no_search">

                    <form action="<?=base_url('stockists/sendsms')?>" method="post" class="form-horizontal">
                        <input type="hidden" name="id" value="<?=$id?>"/>
                        <input type="hidden" name="biz" value="<?=$biz_name?>"/>
                        <div class="form_row">
                            <label for="mobile" class="field_name align_right lblBold">Mobile</label>
                            <div class="field">
                                <select name="mobile" id="mobile" class="span6" >
                                    <option value="">---Please Select Mobile---</option>
                                    <?php
                                    if(!empty($mobiles)){
                                        foreach($mobiles as $mobile) {
                                            if($mobile){?>

                                            <option value="<?=$mobile?>" ><?=$mobile?></option>
                                        <?php  } }
                                    } ?>
                                </select> <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('mobile'); ?></font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="message" class="field_name align_right lblBold">Message</label>
                            <div class="field">
                                <textarea id="message" name="message" placeholder="Message" class="span6" rows="4" value=""><?php echo $message; ?></textarea>
                                <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('message'); ?> </font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label class="field_name align_right"></label>
                            <div class="field">
                                <button type="submit" class="btn btn-large dark_green"><i class="icon-envelope"></i> Send</button>
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

<script src="<?php echo base_url('assets/js/jquery-1.11.1.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery-ui-1.10.3.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap.js'); ?>"></script>

<script src="<?php echo base_url('assets/js/library/jquery.collapsible.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/library/jquery.mCustomScrollbar.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/library/jquery.mousewheel.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/library/jquery.uniform.min.js'); ?>"></script>

<script src="<?php echo base_url('assets/js/library/jquery.autosize-min.js'); ?>"></script>

<script type="text/javascript">
    jQuery(document).ready(function(){
        var region_id = jQuery('#region_id').val();
        if (region_id != ""){
            var post_url = "<?=base_url()?>stockists/get_towns/" + region_id;
            jQuery.ajax({
                type: "POST",
                url: post_url,
                success: function(towns) //we're calling the response json array 'cities'
                {
                    jQuery('#town_id').children('option:not(:first)').remove(); //remove all options except the first option
                    jQuery.each(towns,function(id,name)
                    {
                        var opt = jQuery('<option />'); // here we're creating a new select option for each group

                        opt.val(id);
                        opt.text(name);
                        jQuery('#town_id').append(opt);
                        jQuery('#town_id').val(<?=$town_id?>);
                    });
                } //end success
            }); //end AJAX
        } else {
            jQuery('#town_id').children('option:not(:first)').remove(); //remove all options except the first option
        }//end if
    });

    jQuery('#region_id').change(function(){
        var region_id = jQuery('#region_id').val();
        if (region_id != ""){
            var post_url = "<?=base_url()?>stockists/get_towns/" + region_id;
            jQuery.ajax({
                type: "POST",
                url: post_url,
                success: function(towns) //we're calling the response json array 'cities'
                {
                    jQuery('#town_id').children('option:not(:first)').remove(); //remove all options except the first option
                    jQuery.each(towns,function(id,name)
                    {
                        var opt = jQuery('<option />'); // here we're creating a new select option for each group

                        opt.val(id);
                        opt.text(name);
                        jQuery('#town_id').append(opt);
                    });
                } //end success
            }); //end AJAX
        } else {
            jQuery('#town_id').children('option:not(:first)').remove(); //remove all options except the first option
        }//end if
    }); //end change


</script>

<script src="<?php echo base_url('assets/js/design_core.js'); ?>"></script>


</body>
</html>