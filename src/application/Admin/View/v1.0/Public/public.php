<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <title><?php echo trans('web-title'); ?></title>

    <!--Morris Chart CSS -->

    <?php
    // <!--Morris Chart CSS -->
    //        echo load_css('morris', 'lib/plugins/morris');

    // App css
    echo load_css('bootstrap.min');
    echo load_css('menu');

    echo load_css('components');
    echo load_css('icons');
    echo load_css('core');

    echo load_js('util');

    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>
<script>

</script>
<body class="fixed-left">
<?php echo render_view('public.top-header');?>
<!-- Begin page -->
<div id="lxh-app">
    <div id="wrapper-home" class="wrapper lxh-wrapper">
        <!-- Top Bar Start -->
        <?php //echo render_view('public.top-bar');?>
        <!-- Top Bar End -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php //echo render_view('public.left-sidebar');?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container">

                <iframe src="/admin/index/index" scrolling="no" ></iframe>

                <script id="iframe-tpl" type="text/html">
                    <div id="wrapper-{$name}" class="wrapper lxh-wrapper">
                        <div class="content-page">
                            <div class="content">
                                <div class="container">
                                    <iframe src="{$url}" scrolling="no"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </script>

                </div> <!-- container -->
            </div> <!-- content -->
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->

        <!-- Right Sidebar -->
        <?php echo render_view('public.right-bar');?>
        <!-- /Right-bar -->

    </div>
    <!-- END wrapper -->
</div>

<footer class="footer text-right">
    2016 © Adminto.
</footer>

<script>
    var resizefunc = [];
</script>

<?php

echo load_js('jquery.min');
echo load_js('tab');
//echo load_css('pages');
//echo load_css('responsive');
echo load_css('toastr', 'lib/plugins/toastr');
echo load_js('toastr.min', 'plugins/toastr');

//echo render_view('public.app-js');
// <!-- jQuery  -->
//echo load_js('jquery.min');
//echo load_js('bootstrap.min');
//echo load_js('detect');
//echo load_js('fastclick');
//echo load_js('jquery.blockUI');
echo load_js('waves');
//echo load_js('jquery.nicescroll');
//echo load_js('jquery.slimscroll');
//echo load_js('jquery.scrollTo.min');

// <!-- KNOB JS -->
//

?>

<!-- KNOB JS -->
<!--[if IE]>
<?php //echo load_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
//echo load_js('jquery.knob', 'plugins/jquery-knob');

// <!--Morris Chart-->
//echo load_js('morris.min', 'plugins/morris');
//echo load_js('raphael-min', 'plugins/raphael');

// <!-- Dashboard init -->
//echo load_js('jquery.dashboard', 'pages');

// <!-- App js -->
//echo load_js('jquery.core');
//echo load_js('jquery.app');
?>

</body>
</html>
