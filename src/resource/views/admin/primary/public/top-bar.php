<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <a href="javascript:" class="logo open-left"><?php echo config('admin.logo')?><i class="zmdi zmdi-layers"></i></a>
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
                        <li class="ticket-tab active tab" onclick="TAB.switch('home')" data-action="tab-home" data-name="home" style="min-width:100px;">
                            <a><?php echo trans('Home')?></a>
                            <i class="zmdi zmdi-refresh icon-refresh" onclick='TAB.reload("home", HOMEURL, "<?php echo trans('Home')?>");' style="right:0;"></i>
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
