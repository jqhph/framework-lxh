<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <title>Adminto - Responsive Admin Dashboard Template</title>

    <!--Morris Chart CSS -->

    <?php
        // <!--Morris Chart CSS -->
        load_css('morris', 'plugins/morris');

        // App css
        load_css('bootstrap.min');
        load_css('core');
        load_css('components');
        load_css('icons');
        load_css('pages');
        load_css('menu');
        load_css('responsive');

        load_js('modernizr.min');
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
        <?php echo fetch_view('top-bar', 'Public')?>
        <!-- Top Bar End -->


        <!-- ========== Left Sidebar Start ========== -->
        <?php echo fetch_view('left-sidebar', 'Public')?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container">