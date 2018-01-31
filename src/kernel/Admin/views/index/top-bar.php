<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <a class="logo open-left">
            <?php echo config('admin.logo')?>
            <i class=""><?php echo config('admin.sm-logo');?></i>
        </a>
    </div>
    <style>.page-title a{color:#505458}</style>
    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <?php if (0) {?>
            <div class="pull-left">
                <button class="button-menu-mobile open-left" style="color:#fff;line-height:60px;margin-left:12px;font-size:18px;">
                    <i class="zmdi zmdi-menu"></i></button></div>
            <?php } ?>

            <div class="header-flex-box">
                <nav class="header-tag">
                    <ul class="header-tag-list a tab-menu">
                        <li class="ticket-tab active tab" onclick="TAB.switch('home')" data-action="tab-home" data-name="home" style="min-width:100px;">
                            <a><?php echo trans('Home')?></a>
                            <i class="zmdi zmdi-refresh icon-refresh" onclick='TAB.reload("home", HOMEURL, "<?php echo trans('Home')?>");' style="right:0;"></i>
                        </li>
                    </ul>
                </nav>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <?php if ($useGlobalSearchInput) {?>
                <li class="hidden-xs">
                    <form role="search" class="app-search"><input type="text" placeholder="<?php echo trans('Search...');?>" class="form-control"><a href=""><i class="fa fa-search"></i></a></form>
                </li>
                <?php } ?>
                <?php echo $content; ?>
            </ul>
        </div>

    </div>
    </div>

</div>

<script id="header-tab-tpl" type="text/html">
    <li class="ticket-tab tab active " data-action="tab-{name}" data-name="{name}">
        <a class="tab-label">{label}</a>
        <i class="zmdi zmdi-refresh icon-refresh"></i>
        <i class="zmdi zmdi-close tab-close icon-close" ></i>
    </li>
</script>
