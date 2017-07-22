<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="<?php echo Lxh\Kernel\AdminUrlCreator::makeHome();?>" class="logo"><span>L<span>xh</span></span><i class="zmdi zmdi-layers"></i></a>
    </div>
    <style>
        .page-title a{color:#505458}
    </style>
    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">

            <!-- Page title -->
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <button class="button-menu-mobile open-left">
                        <i class="zmdi zmdi-menu"></i>
                    </button>
                </li>
                <li>
                    <h5 class="page-title"><?php echo empty($navTitle) ? make('acl-menu')->makeNav() : trans($navTitle);?></h5>
                </li>
            </ul>

            <!-- Right(Notification and Searchbox -->
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
                    <form role="search" class="app-search">
                        <input type="text" placeholder="<?php echo trans_with_global('Search');?>..."
                               class="form-control">
                        <a href=""><i class="fa fa-search"></i></a>
                    </form>
                </li>
            </ul>

        </div><!-- end container -->
    </div><!-- end navbar -->
</div>
<!--<ol class="breadcrumb">-->
<!--    <li><a href="#">Menu management</a></li><li>Modify menu</li>-->
<!--</ol>-->