<div id="main_navigation" class="dark_navigation"> <!-- Main navigation start -->
    <div class="inner_navigation">
        <ul class="main">
            <li class="active navAct"><a id="current" href="<?=base_url('dashboard')?>"><i class="icon-dashboard"></i> Dashboard</a></li>


            <li><a href=""><i class="icon-envelope"></i>SMS</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('sms/sent')?>">SMS Sent</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-puzzle-piece"></i>Groups</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('groups')?>">View Groups</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-comments"></i> Group Messages</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('messages/received')?>">Messages Received</a></li>
                    <li><a href="<?=base_url('messages/replied')?>">Messages Replied</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-file-text"></i> Reports</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('reports/purchases')?>">Purchase Report</a></li>
                    <li><a href="<?=base_url('reports/detailed_purchases')?>">Detailed Purchase Report</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-tags"></i>Products</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('products')?>">View Products</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-group"></i>Stockists</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('stockists')?>">View Stockists</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-group"></i>Distributors</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('distributors')?>">View Distributors</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-group"></i>Farmers</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('farmers')?>">View Farmers</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-map-marker"></i>Towns</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('towns')?>">View Towns</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-globe"></i>Regions</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('regions')?>">View Regions</a></li>
                </ul>
            </li>

        </ul>
    </div>
</div>