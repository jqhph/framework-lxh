<?php $url = Lxh\Admin\Admin::url(); ?>
<li class="dropdown user user-menu">
    <a class="dropdown-toggle" data-toggle="dropdown">
         <span class="user-box">
            <span class="user-img">
                <img src="<?php echo $avatar?>" alt="user-img" class="img-circle img-responsive">
                <div class="user-status online" ><i class="zmdi zmdi-dot-circle"></i></div>
                <span class="hidden-xs">&nbsp;&nbsp;<?php echo $name?></span>
            </span>
        </span>
    </a>
    <ul class="dropdown-menu dm-icon pull-right" style="text-transform:uppercase">
        <li class="hidden-xs">
            <a href="javascript:open_tab('admin-setting', '<?php echo $url->profile()?>', '<?php echo trans('Profile')?>')"><i class="zmdi zmdi-account"></i> <?php echo trans('Setting')?></a>
        </li>
        <li class="hidden-xs">
            <a target="_blank" href="<?php echo $url->home()?>"><i class="zmdi zmdi-view-web"></i> <?php echo trans('View Site')?></a>
        </li>
        <li class="divider"></li>
        <li class="hidden-xs">
            <a href="<?php echo $url->logout()?>"><i class="zmdi zmdi-power"></i> <?php echo trans('Sign out')?></a>
        </li>
    </ul>
</li>