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
            <li class="active"><a>Dashboard</a></li>
        </ul>
    </div>
    <div class="inner_content">
        <div class="statistic clearfix">
            <div class="current_page pull-left">
                <span><i class="icon-laptop"></i> Dashboard</span> <span class="hidden-480 quote">- Twiga Chemicals SMS Portal</span>
            </div>
        </div>

        <div class="report-widgets">
            <div class="row-fluid">
                <div class="span4">
                    <div class="widget yellow clearfix">
                        <div class="content">
                            <div class="icon">
                                <i class="icon-envelope"></i>
                                Received Today
                            </div>
                            <div class="value" id="visitors_count">
                                <?= $todays_total; ?>
                            </div>
                        </div>
                        <a href="" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
                <div class="span4">
                    <div class="widget dark_turq clearfix">
                        <div class="content">
                            <div class="icon">
                                <i class="icon-envelope"></i>
                                Received last 7 days
                            </div>
                            <div class="value" id="today_reports">
                                <?= $weeks_total; ?>
                            </div>
                        </div>
                        <a href="" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
                <div class="span4">
                    <div class="widget orange clearfix">
                        <div class="content">
                            <div class="icon">
                                <i class="icon-envelope"></i>
                                Received Last 30 days
                            </div>
                            <div class="value" id="total_reports">
                                <?= $months_total; ?>
                            </div>
                        </div>
                        <a href="" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-widgets">
            <div class="row-fluid">
                <div class="span4">
                    <div class="widget blue clearfix">
                        <div class="options">
                            <ul>
                                <li><a href=""><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-group"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="surveys_count">
                                <?= $stockists_total; ?>
                            </div>
                            <div class="description">
                                No of Stockists
                            </div>
                        </div>
                        <a href="<?=base_url("stockists")?>" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="span4">
                    <div class="widget grey clearfix">
                        <div class="options">
                            <ul>
                                <li><a href=""><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-group"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="active_survey">
                                <?= $distributors_total; ?>
                            </div>
                            <div class="description">
                                No of Distributors
                            </div>
                        </div>
                        <a href="<?=base_url("distributors")?>" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="span4">
                    <div class="widget red clearfix">
                        <div class="options">
                            <ul>

                                <li><a href=""><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-group"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="category_count">
                                <?= $farmers_total; ?>
                            </div>
                            <div class="description">
                                No of Farmers
                            </div>
                        </div>
                        <a href="" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <hr/>
        <div class="status-widgets">
            <div class="row-fluid">
                <div class="span4">
                    <div class="widget blue clearfix">
                        <div class="options">
                            <ul>
                                <li><a href=""><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-puzzle-piece"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="surveys_count">
                                <?= $products_total; ?>
                            </div>
                            <div class="description">
                                Total Products
                            </div>
                        </div>
                        <a href="<?=base_url("stockists")?>" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="span4">
                    <div class="widget grey clearfix">
                        <div class="options">
                            <ul>
                                <li><a href=""><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-group"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="active_survey">
                                <?= $groups_total; ?>
                            </div>
                            <div class="description">
                                Total Groups
                            </div>
                        </div>
                        <a href="<?=base_url("distributors")?>" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="span4">
                    <div class="widget red clearfix">
                        <div class="options">
                            <ul>
                                <li><a href="#"><i class="icon-cog"></i></a></li>
                                <li><a href="#"><i class="icon-refresh"></i></a></li>
                            </ul>
                            <i class="icon-list-alt"></i>
                        </div>
                        <div class="details">
                            <div class="number" id="category_count">
                                <?= $blacklist_total; ?>
                            </div>
                            <div class="description">
                                Total Blacklisted
                            </div>
                        </div>
                        <a href="" class="more"><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
    </div>
</div>




<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo($base); ?>assets/js/jquery-1.11.1.js"></script>
<script src="<?php echo($base); ?>assets/js/jquery-1.10.2.min.js"></script>
<script src="<?php echo($base); ?>assets/js/jquery-ui-1.10.3.js"></script>
<script src="<?php echo($base); ?>assets/js/bootstrap.js"></script>

<script src="<?php echo($base); ?>assets/js/library/jquery.collapsible.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/jquery.mCustomScrollbar.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/jquery.mousewheel.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/jquery.uniform.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/jquery.sparkline.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/chosen.jquery.min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/jquery.autosize-min.js"></script>
<script src="<?php echo($base); ?>assets/js/library/footable/footable.js"></script>

<script src="<?php echo($base); ?>assets/js/design_core.js"></script>
<script>


    /* $('document').ready( function(){
     $.ajax({
     type: "GET",
     url: "GetNoCategories",
     success: function(data){
     $('#cat_widget').html(data);
     console.log("Success");
     alert(data);
     },
     fail: function(){
     console.log("Failed");
     }
     });
     loadJSON();

     });*/

    $('document').ready( function(){
/*        $.ajax({
            type: "GET",
            url: "GetDashboardData",
            success: function(data){
                var jsonObj = JSON.parse(data);
                $('#visitors_count').html(jsonObj.visitors);
                $('#today_reports').html(jsonObj.creports);
                $('#total_reports').html(jsonObj.ireports);
                $('#surveys_count').html(jsonObj.surveys_count);
                $('#active_survey').html(jsonObj.active_survey);
                $('#category_count').html(jsonObj.categories_count);
                console.log("Success");
            }
        });*/

    });

    /*function loadJSON(){
     var jsonObj = JSON.parse('{"name": "brett", "country": "Australia"}');
     $('#cat_widget').html(jsonObj.name);
     }*/
    jQuery(function($) {
        $('.footable').footable();
        $('.responsive_table_scroll').mCustomScrollbar({
            set_height: 400,
            advanced:{
                updateOnContentResize: true,
                updateOnBrowserResize: true
            }
        });
    });
</script>

</body>
</html>