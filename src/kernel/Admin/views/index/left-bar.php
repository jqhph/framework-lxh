<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <?php if ($users) {?>
        <div class="user-box"><?php echo $users?></div>
        <?php } else {
            echo '<div style="height:10px;"></div>';
        }?>

        <div id="sidebar-menu">
            <ul>
                <?php if ($title) {?>
                <li class="text-muted menu-title"><?php echo $title?></li>
                <?php } ?>

                <li>
                    <a id="menu-home" href="javascript:LXHSTORE.TAB.switch('home', '<?php echo $home;?>', '<?php echo trans('Home')?>')" class="waves-effect active "><i class="zmdi zmdi-view-dashboard"></i> <span>
                            <?php echo trans('Dashboard', 'menus');?> </span> </a>
                </li>

                <?php foreach ($menu->get() as &$m) { ?>
                    <li class="has_sub">
                        <a id="menu-<?php echo $m['id']?>" href="javascript:<?php echo empty($m['subs']) && !empty($m['route']) ? "LXHSTORE.TAB.switch('menu-{$m['id']}', '{$m['route']}', '{$m['name']}')" : '';?>"
                           class="waves-effect ">
                            <i class="<?php echo $m['icon'];?>"></i>
                            <span><?php echo $m['name'];?></span>
                            <?php if (! empty($m['subs'])) { ?>
                                <span class="menu-arrow"></span>
                            <?php } ?>
                        </a>

                        <?php if (! empty($m['subs'])) { ?>
                            <ul class="list-unstyled">
                                <?php foreach ($m['subs'] as & $sub) { ?>
                                    <li>
                                        <a id="menu-<?php echo $sub['id']?>"
                                           href="javascript:LXHSTORE.TAB.switch('menu-<?php echo $sub['id'];?>', '<?php echo $sub['route'];?>', '<?php echo $sub['name']?>')">
                                            <?php echo $sub['name'];?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php } ?>

                <?php
                $plugins = $menu->renderPlugins();
                if ($plugins) { ?>
                <li class="has_sub">
                    <a class="waves-effect ">
                        <i class="fa fa-plug"></i>
                        <span><?php echo trans('Plugins', 'menus');?></span><span class="menu-arrow"></span>
                    </a>
                    <ul class="list-unstyled"><?php echo $plugins;?></ul>
                </li>
                <?php }?>

            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
</div>