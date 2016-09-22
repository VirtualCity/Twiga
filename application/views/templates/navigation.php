<div id="main_navigation" class="dark_navigation"> <!-- Main navigation start -->
    <div class="inner_navigation">
        <ul class="main">
            <li class="active navAct"><a id="current" href="<?=base_url('dashboard')?>"><i class="icon-dashboard"></i> Dashboard</a></li>

            <?Php $role = $this->session->userdata('role');
            if($role!=="User"){
                ?>

            <?Php
            }
            ?>
            <li><a href=""><i class="icon-envelope"></i>SMS</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('sms/newsms')?>">New SMS</a></li>
                    <li><a href="<?=base_url('sms/newbulksms')?>">New Bulk SMS</a></li>
                    <li><a href="<?=base_url('sms/sent')?>">SMS Sent</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-puzzle-piece"></i>Groups</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('groups')?>">View Groups</a></li>
                    <li><a href="<?=base_url('groups/add')?>">Add Group</a></li>
                    <li><a href="<?=base_url('groups/import')?>">Import to Group</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-comments"></i>Group Messages</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('messages/received')?>">Messages Received</a></li>
                    <li><a href="<?=base_url('messages/replied')?>">Messages Replied</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-file-text"></i> Reports</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('reports/purchases')?>">Purchase Report</a></li>
                    <li><a href="<?=base_url('reports/detailed_purchases')?>">Detailed Purchase Report</a></li>
                    <li><a href="<?=base_url('reports/tas')?>">Email TA's Weekly Report</a></li>
                    <li><a href="<?=base_url('reports/managers')?>">Email Managers Weekly Report</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-tags"></i>Products</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('products')?>">View Products</a></li>
                    <li><a href="<?=base_url('products/add')?>">Add Product</a></li>
                    <li><a href="<?=base_url('products/import')?>">Import Products</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-group"></i>Stockists</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('stockists')?>">View Stockists</a></li>
                    <li><a href="<?=base_url('stockists/add')?>">Add Stockist</a></li>
                    <li><a href="<?=base_url('stockists/import')?>">Import Stockists</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-truck"></i>Distributors</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('distributors')?>">View Distributors</a></li>
                    <li><a href="<?=base_url('distributors/add')?>">Add Distributor</a></li>
                    <li><a href="<?=base_url('distributors/import')?>">Import Distributors</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-group"></i>Farmers</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('farmers')?>">View Farmers</a></li>
                    <li><a href="<?=base_url('farmers/add')?>">Add Farmer</a></li>
                    <li><a href="<?=base_url('farmers/import')?>">Import Farmers</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-circle-blank"></i>Technical Assistants</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('tas')?>">View TA's</a></li>
                    <li><a href="<?=base_url('tas/add')?>">Add TA</a></li>
                    <li><a href="<?=base_url('tas/import')?>">Import TA's</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-circle"></i>Area Managers</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('managers')?>">View Managers</a></li>
                    <li><a href="<?=base_url('managers/add')?>">Add Manager</a></li>
                    <li><a href="<?=base_url('managers/import')?>">Import Managers</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-map-marker"></i> Towns</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('towns')?>">View Towns</a></li>
                    <li><a href="<?=base_url('towns/add')?>">Add Town</a></li>
                    <li><a href="<?=base_url('towns/import')?>">Import Towns</a></li>
                </ul>
            </li>
            <li><a href=""><i class="icon-globe"></i>Regions</a>
                <ul class="sub_main">
                    <li><a href="<?=base_url('regions')?>">View Regions</a></li>
                    <li><a href="<?=base_url('regions/add')?>">Add Region</a></li>
                    <li><a href="<?=base_url('regions/import')?>">Import Regions</a></li>
                </ul>
            </li>
            <?Php
            if($role==="ADMIN"){
                ?>
                <li><a href=""><i class="icon-user"></i> User Management</a>
                    <ul class="sub_main">
                        <li><a href="<?=base_url('users')?>">View Users</a></li>
                        <li><a href="<?=base_url('adduser')?>">Add User</a></li>
                        <li><a href="<?=base_url('addblacklist')?>">Blacklist Number</a></li>
                        <li><a href="<?=base_url('blacklist')?>">View Blacklist</a></li>
                    </ul>
                </li>
                <li><a href=""><i class="icon-cogs"></i> App Settings</a>
                    <ul class="sub_main">
                        <li><a href="<?=base_url('settings/email')?>">Email setup</a></li>
                    </ul>
                </li>
            <?Php } ?>
        </ul>
    </div>
</div>