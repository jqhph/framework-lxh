<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!-- User -->
        <div class="user-box"><?php echo $users?></div>
        <!-- End User -->

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <?php if ($title) {?>
                <li class="text-muted menu-title"><?php echo $title?></li>
                <?php } ?>

                <li>
                    <a onclick="TAB.switch('home', '<?php echo $home;?>', '<?php echo trans('Home')?>')" class="waves-effect
                    <?php if ($menu->isActive('Index', 'Dashboard')) echo 'active';?> "><i class="zmdi zmdi-view-dashboard"></i> <span>
                            <?php echo trans('Dashboard', 'menus');?> </span> </a>
                </li>

                <?php foreach ($menu->get() as & $m) { ?>
                    <li class="has_sub">
                        <a onclick="<?php echo empty($m['subs']) && !empty($m['controller']) ? "TAB.switch({$m['id']}, '{$m['url']}', '{$m['name']}')" : '';?>"
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