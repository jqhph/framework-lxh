<?php
$menu = make('acl-menu');
$user = admin();
$name = $user->first_name . $user->last_name;
?>

<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            <div class="user-img">
                <img src="<?php echo load_img('users/avatar-1.jpg')?>" alt="user-img" title="Mat Helme" class="img-circle img-thumbnail img-responsive">
                <div class="user-status offline"><i class="zmdi zmdi-dot-circle"></i></div>
            </div>
            <h5><a href="#"><?php echo $name ?: $user->username ?></a> </h5>
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
                    <a onclick="TAB.switch('home', '<?php echo Lxh\Admin\Kernel\Url::makeHome();?>', '<?php echo trans('Home')?>')" class="waves-effect
                    <?php if ($menu->isActive('Index', 'Index')) echo 'active';?> "><i class="zmdi zmdi-home"></i> <span>
                            <?php echo trans('Home', 'menus');?> </span> </a>
                </li>

                <?php foreach ($menu->get() as & $m) { ?>
                <li class="has_sub">
                    <a onclick="<?php echo empty($m['subs']) ? "TAB.switch({$m['id']}, '{$m['url']}', '{$m['name']}')" : '';?>"
                       class="waves-effect <?php if ($menu->isActive($m['controller'], $m['action'])) echo 'active';?>">
                        <i class="<?php echo $m['icon'];?>"></i>
                        <span><?php echo $m['name'];?></span>
                        <?php if (! empty($m['subs'])) { ?>
                        <span class="menu-arrow"></span>
                        <?php } ?>
                    </a>

                    <?php if (! empty($m['subs'])) { ?>
                    <ul class="list-unstyled">
                        <?php foreach ($m['subs'] as & $sub) { ?>
                        <li class="<?php if ($menu->isActive($sub['controller'], $sub['action'])) echo 'active';?>">
                            <a class="<?php if ($menu->isActive($sub['controller'], $sub['action'])) echo 'active';?>"
                               onclick="TAB.switch(<?php echo $sub['id'];?>, '<?php echo $sub['url'];?>', '<?php echo $sub['name']?>')">
                                <?php echo $sub['name'];?></a></li>
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