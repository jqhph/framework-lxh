<style>
    .layadmin-pagetabs {
        height: 40px;
        line-height: 40px;
        padding: 0 80px 0 40px;
        background-color: #fff;
        box-sizing: border-box;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.1);
    }

    .layadmin-pagetabs, .layui-layout-admin .layui-body, .layui-layout-admin .layui-footer, .layui-layout-admin .layui-header .layui-layout-right, .layui-layout-admin .layui-header .layui-nav .layui-nav-item, .layui-layout-admin .layui-layout-left, .layui-layout-admin .layui-logo, .layui-layout-admin .layui-side {
        transition:all .3s;-webkit-transition:all .3s;}
    .layadmin-pagetabs {position:fixed;top:53px;right:0;z-index:999;}
    .layadmin-pagetabs, .layui-layout-admin .layui-body, .layui-layout-admin .layui-footer, .layui-layout-admin .layui-layout-left{left:55px;}
    .layadmin-pagetabs .layadmin-tabs-control {
        position: absolute;
        top: 0;
        width: 40px;
        height: 100%;
        text-align: center;
        cursor: pointer;
        transition: all .3s;
        -webkit-transition: all .3s;
        box-sizing: border-box;
        border-left: 1px solid #f6f6f6;
    }
    .layadmin-pagetabs .layui-tab {
        margin: 0;
        overflow: hidden;
    }
    .layui-tab {
        margin: 10px 0;
        text-align: left!important;
    }
    .layadmin-pagetabs .layui-tab-title {
        height: 40px;
        border: none;
    }
    .layui-tab-title {
        position: relative;
        left: 0;
        height: 40px;
        white-space: nowrap;
        font-size: 0;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        transition: all .2s;
        -webkit-transition: all .2s;
    }
    .layadmin-pagetabs .layui-tab-title li:first-child {
        padding-right: 15px;
    }
    .layadmin-pagetabs .layui-tab-title li {
        min-width: 0;
        line-height: 40px;
        max-width: 160px;
        text-overflow: ellipsis;
        padding-right: 40px;
        overflow: hidden;
        border-right: 1px solid #f6f6f6;
        vertical-align: top;
    }
    .layui-tab-title li {
        display: inline-block;
        vertical-align: middle;
        font-size: 14px;
        transition: all .2s;
        -webkit-transition: all .2s;
        position: relative;
        line-height: 40px;
        min-width: 65px;
        padding: 0 15px;
        text-align: center;
        cursor: pointer;
    }
</style>

<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <a class="logo" href="<?php echo Lxh\Admin\Admin::url()->index()?>">
            <?php echo config('admin.logo')?>
            <i class=""><?php echo config('admin.sm-logo');?></i>
        </a>
    </div>
    <style>.page-title a{color:#505458}</style>
    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default <?php echo config('admin.navbar-theme')?>" role="navigation">
        <div class="container">
            <?php if (0) {?>
            <div class="pull-left">
                <button class="button-menu-mobile open-left" style="color:#fff;line-height:60px;margin-left:12px;font-size:18px;">
                    <i class="zmdi zmdi-menu"></i></button></div>
            <?php } ?>

            <div class="header-flex-box">
                <nav class="header-tag">
                    <ul class="header-tag-list a tab-menu">
                        <li class="ticket-tab active tab" onclick="LXHSTORE.TAB.switch('home')" data-action="tab-home" data-name="home" style="min-width:100px;">
                            <a><?php echo trans('Home')?></a>
                            <i class="zmdi zmdi-refresh icon-refresh" onclick='LXHSTORE.TAB.reload("home", LXHSTORE.HOMEURL, "<?php echo trans('Home')?>");' style="right:0;"></i>
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
<div class="layadmin-pagetabs" id="LAY_app_tabs">
    <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-down">
        <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
    </div>
    <div class="layui-tab" lay-unauto="" lay-allowclose="true" lay-filter="layadmin-layout-tabs">
        <ul class="layui-tab-title" id="LAY_app_tabsheader">
            <li lay-id="home/console.html" lay-attr="home/console.html" class=""><i class="layui-icon layui-icon-home"></i><i class="layui-icon layui-unselect layui-tab-close">ဆ</i></li>
            <li lay-id="home/homepage1.html" lay-attr="home/homepage1.html" class="layui-this"><span>主页一</span><i class="layui-icon layui-unselect layui-tab-close">ဆ</i></li></ul>
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
