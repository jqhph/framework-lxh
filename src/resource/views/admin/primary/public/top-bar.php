<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <a href="<?php echo Lxh\Admin\Kernel\Url::makeHome();?>" class="logo">
            <span><?php echo trans_with_global('first-logo-text', 'labels'); ?><span>
                    <?php echo trans_with_global('last-logo-text', 'labels'); ?></span></span><i class="zmdi zmdi-layers"></i></a>
    </div>
    <style>
        .page-title a{color:#505458}
    </style>
    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="header-flex-box">
                <nav class="header-tag">
                    <ul class="header-tag-list a tab-menu">
                        <li class="ticket-tab active tab"  data-action="tab-home" data-name="home" style="min-width:100px;">
                            <a><?php echo trans('Home')?></a>
                            <i class="zmdi zmdi-refresh icon-refresh" onclick='document.getElementById("home-iframe").contentWindow.location.reload(true)' style="right:0;"></i>
                        </li>
                    </ul>
                </nav>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <!-- Notification -->
                    <div class="notification-box">
                        <ul class="list-inline m-b-0">
                            <li>
                                <a href="javascript:void(0);" class="right-bar-toggle">
                                    <i class="zmdi zmdi-notifications-none"></i>
                                </a>
                                <div class="noti-dot">
                                    <span class="dot"></span>
                                    <span class="pulse"></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- End Notification bar -->
                </li>
                <li class="hidden-xs">
                </li>
            </ul>
        </div>

    </div>
    <div class="topbar-left">
        <a></a>
    </div>

</div>

<script id="header-tab-tpl" type="text/html">
    <li class="ticket-tab tab active " data-action="tab-{name}" data-name="{name}">
        <a class="tab-label">{label}</a>
        <i class="zmdi zmdi-refresh icon-refresh"></i>
        <i class="zmdi zmdi-close tab-close icon-close" ></i>
    </li>
</script>
<!--<ol class="breadcrumb">-->
<!--    <li><a href="#">Menu management</a></li><li>Modify menu</li>-->
<!--</ol>-->