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
            <li><a href="<?=base_url("distributors")?>">Distributors</a><span class="divider">/</span></li>
            <li class="active"><a>Add Distributor</a></li>
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
                    <h5>Add Distributor</h5>
                </div>
                <div class="well-content no_search">

                    <form action="<?=base_url('distributors/add')?>" method="post" class="form-horizontal">

                        <div class="form_row">
                            <label for="code" class="field_name align_right lblBold">Distributor Code </label>
                            <div class="field">
                                <input type="text" name="code" id="code" placeholder="Distributor Code" class="span6" value="<?=$code?>"/>
                                <font color="red"> *</font>
                                <div><font color="red"> <?php echo form_error('code'); ?> </font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="biz_name" class="field_name align_right lblBold">Distributor Name </label>
                            <div class="field">
                                <input type="text" name="biz_name" id="biz_name" placeholder="Distributor Name" class="span6" value="<?=$biz_name?>""/>
                                <font color="red"> *</font>
                                <div><font color="red"> <?php echo form_error('biz_name'); ?> </font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="name" class="field_name align_right lblBold">Contact Person </label>
                            <div class="field">
                                <input type="text" name="name" id="name" placeholder="Name of Contact Person" class="span6" value="<?=$name?>""/>
                                <font color="red"> *</font>
                                <div><font color="red"> <?php echo form_error('name'); ?> </font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="mobile" class="field_name align_right lblBold">Mobile Number </label>
                            <div class="field">
                                <input type="text" name="mobile" id="mobile" placeholder="254" class="span6" value="<?=$mobile?>""/>
                                <font color="red"> *</font>
                                <div><font color="red"> <?php echo form_error('mobile'); ?> </font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="email" class="field_name align_right lblBold">Email </label>
                            <div class="field">
                                <input type="text" name="email" id="email" placeholder="Email" class="span6" value="<?=$email?>""/>

                                <div><font color="red"> <?php echo form_error('email'); ?> </font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="region" class="field_name align_right lblBold">Region</label>
                            <div class="field">
                                <select name="region" id="region" class="span6" rows="4" >
                                    <option value="">---Please Select Region---</option>
                                    <?php
                                    if(!empty($areas)){
                                        foreach($areas as $row) { ?>
                                            <option value="<?=$row->id?>" <?php if ($row->id ===$region_id){echo "selected";}?>><?=$row->name?></option>
                                        <?php   }
                                    } ?>
                                </select> <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('region'); ?></font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="town_id" class="field_name align_right lblBold">Town</label>
                            <div class="field">
                                <select name="town_id" id="town_id" class="span6" rows="4" >
                                    <option value="">---Please Select a Town---</option>
                                    <?php
                                    /*if(!empty($towns)){
                                        foreach($towns as $town) { */?><!--
                                            <option value="<?/*=$town->id*/?>" <?php /*if ($town->id ===$town_id){echo "selected";}*/?>><?/*=$town->name*/?></option>
                                        --><?php /*  }
                                    } */?>
                                </select> <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('town_id'); ?></font></div>
                            </div>
                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label for="description" class="field_name align_right lblBold">Status</label>
                            <div class="field">
                                <select name="status" id="status" class="span6" >
                                    <option value="ACTIVE" <?php if ($status ==="ACTIVE"){echo " selected";}?>>ACTIVE</option>
                                    <option value="INACTIVE" <?php if ($status ==="INACTIVE"){echo " selected";}?>>INACTIVE</option>
                                </select> <font color="red"> *</font>
                                <div><font color="red"><?php echo form_error('status'); ?></font></div>
                            </div>

                        </div>
                        <hr class="field-separator">
                        <div class="form_row">
                            <label class="field_name align_right"></label>
                            <div class="field">
                                <button type="submit" class="btn btn-large dark_green"><i class="icon-plus"></i> Add Distributor</button>
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
        var region_id = jQuery('#region').val();
        if (region_id != ""){
            var post_url = "<?=base_url()?>distributors/get_towns/" + region_id;
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
        }
    });

    jQuery('#region').change(function(){


        var region_id = jQuery('#region').val();
        if (region_id != ""){
            var post_url = "<?=base_url()?>distributors/get_towns/" + region_id;
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