<header class="blue">
    <a href="<?=base_url('dashboard')?>" class="logo_image"><span class="hidden-480 applogo">Twiga Chemicals </span><span class="hidden-768 txtSmall">SMS Portal</span> </a>
    <ul class="header_actions">
        <li class="dropdown"><a href=""><img src="<?= base_url('assets/img/avatars/user.png') ?>" alt="User image" class="avatar"><?Php echo($this->session->userdata('fname')." ".$this->session->userdata('sname')); ?><i class="icon-angle-down"></i></a>
            <ul>
                <li><a href="<?=base_url('password')?>"><i class="icon-cog"></i> Change password</a></li>
                <li><a href="<?=base_url('logout')?>"><i class="icon-remove"></i> Logout</a></li>
            </ul>
        </li>
        <li><a href="<?=base_url('logout')?>"><i class="icon-signout"></i> <span class="hidden-768 hidden-480">Logout</span></a></li>
        <li class="responsive_menu"><a class="iconic" href=""><i class="icon-reorder"></i></a></li>
    </ul>
</header>