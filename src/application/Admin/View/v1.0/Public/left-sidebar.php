<?php
$menu = make('acl-menu');
?>

<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            <div class="user-img">
                <img src="<?php echo load_img('users/avatar-1.jpg')?>" alt="user-img" title="Mat Helme" class="img-circle img-thumbnail img-responsive">
                <div class="user-status offline"><i class="zmdi zmdi-dot-circle"></i></div>
            </div>
            <h5><a href="#">Mat Helme</a> </h5>
            <ul class="list-inline">
                <li>
                    <a href="#" >
                        <i class="zmdi zmdi-settings"></i>
                    </a>
                </li>

                <li>
                    <a href="#" class="text-custom">
                        <i class="zmdi zmdi-power"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End User -->

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
<!--                <li class="text-muted menu-title">Navigation</li>-->

                <li>
                    <a href="<?php echo Lxh\Kernel\AdminUrlCreator::makeHome();?>" class="waves-effect
                    <?php if ($menu->isActive('Index', 'Index')) echo 'active';?> "><i class="zmdi zmdi-view-dashboard"></i> <span>
                            <?php echo trans('Home');?> </span> </a>
                </li>

                <?php foreach ($menu->get() as & $m) { ?>
                <li class="has_sub">
                    <a href="<?php echo empty($m['subs']) ? $m['url'] : 'javascript:void(0);';?>"
                       class="waves-effect <?php if ($menu->isActive($m['controller'], $m['action'])) echo 'active';?>">
                        <i class="<?php echo $m['icon'];?>"></i>
                        <span><?php echo trans_with_global($m['name'], 'menu');?></span>
                        <?php if (! empty($m['subs'])) { ?>
                        <span class="menu-arrow"></span>
                        <?php } ?>
                    </a>

                    <?php if (! empty($m['subs'])) { ?>
                    <ul class="list-unstyled">
                        <?php foreach ($m['subs'] as & $sub) { ?>
                        <li class="<?php if ($menu->isActive($sub['controller'], $sub['action'])) echo 'active';?>">
                            <a class="<?php if ($menu->isActive($sub['controller'], $sub['action'])) echo 'active';?>" href="<?php echo $sub['url'];?>">
                                <?php echo trans_with_global($sub['name'], 'menu');?></a></li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>

            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>