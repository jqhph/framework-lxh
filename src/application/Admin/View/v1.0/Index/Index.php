<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <title>Adminto - Responsive Admin Dashboard Template</title>

    <?php

    echo fetch_view('SPA-js', 'Public');
    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


</head>
<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">
    <!-- Top Bar Start -->
    <!--    --><?php //echo fetch_view('top-bar', 'Public')?>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <!--    --><?php //echo fetch_view('left-sidebar', 'Public')?>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">


            </div> <!-- container -->
        </div> <!-- content -->
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <!-- Right Sidebar -->
    <?php //echo fetch_view('right-bar', 'Public');?>
    <!-- /Right-bar -->

</div>
<!-- END wrapper -->

<footer class="footer text-right">
    2016 © Adminto.
</footer>

<script>
    var resizefunc = [];
</script>

<?php
// <!-- jQuery  -->
//load_js('jquery.min');
//load_js('bootstrap.min');
//load_js('detect');
//load_js('fastclick');
//load_js('jquery.blockUI');
//load_js('waves');
//load_js('jquery.nicescroll');
//load_js('jquery.slimscroll');
//load_js('jquery.scrollTo.min');

// <!-- KNOB JS -->
//

?>

<!-- KNOB JS -->
<!--[if IE]>
<?php //load_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
//load_js('jquery.knob', 'plugins/jquery-knob');
//
//// <!--Morris Chart-->
//load_js('morris.min', 'plugins/morris');
//load_js('raphael-min', 'plugins/raphael');
//
//// <!-- Dashboard init -->
//load_js('jquery.dashboard', 'pages');
//
//// <!-- App js -->
//load_js('jquery.core');
//load_js('jquery.app');
?>

</body>
</html>