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
//        load_css('morris', 'lib/plugins/morris');


        load_css('bootstrap.min');
        // App css

        load_css('menu');

//    'lib/plugins/toastr/toastr.min.css',
//    's/css/core.css',
//    's/css/components.css',
//    's/css/icons.css',
//    's/css/pages.css',
//    's/css/responsive.css'

    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


</head>
<script>
    // 添加需要引入的js
    window.jsLibArr = {}
    function add_js(data) {
        if (typeof data == 'string') {
            jsLibArr[data] = data
        } else {
            for (var i in data) {
                jsLibArr[i] = data[i]
            }
        }
    }

    // 添加需要引入的css
    window.cssLibArr = []
    function add_css(data) {
        if (typeof data == 'string') {
            cssLibArr.push(data)
        } else {
            for (var i in data) {
                cssLibArr.push(data[i])
            }
        }
    }
</script>
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
                    <div style="height:8px;"></div>