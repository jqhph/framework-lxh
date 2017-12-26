<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="<?php echo config('admin.favicon')?>">

    <title><?php echo config('admin.title'); ?></title>

    <?php
    // App css
    echo load_css('bootstrap.min');
    echo load_css('menu-light');

//    echo load_css('components');
    echo load_css('icons');
    echo load_css('core');

    echo load_js('util');
    echo load_js('jquery.min');
    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        .loading{position:absolute;top:50%;left:48.5%;margin-top:-30px;z-index:999999;width:100px;text-align:center}.loading>div{width:20px;height:20px;background-color:#36d5ac;border-radius:100%;display:inline-block;-webkit-animation:bouncedelay 1.4s infinite ease-in-out;animation:bouncedelay 1.4s infinite ease-in-out;-webkit-animation-fill-mode:both;animation-fill-mode:both}.loading .loading1{-webkit-animation-delay:-.32s;animation-delay:-.32s}.loading .loading2{-webkit-animation-delay:-.16s;animation-delay:-.16s}@-webkit-keyframes bouncedelay{0%,100%,80%{-webkit-transform:scale(0)}40%{-webkit-transform:scale(1)}}@keyframes bouncedelay{0%,100%,80%{transform:scale(0);-webkit-transform:scale(0)}40%{transform:scale(1);-webkit-transform:scale(1)}}.loading-circle{position:relative;display:inline-block;line-height:0;vertical-align:middle;box-sizing:border-box}.loading-circle::after{content:"";display:inline-block;border:3px solid rgba(0,0,0,.1);border-radius:50%;height:32px;width:32px;box-sizing:border-box}.loading-circle:after{position:absolute;left:0;top:0;border-color:#21d376 transparent transparent;animation:loading .6s linear infinite}.loading-circle-sm::after,.loading-circle-sm::before{height:18px;width:18px;border-width:2px}.loading-circle-xs::after,.loading-circle-xs::before{height:12px;width:12px;border-width:2px}@keyframes loading{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
    </style>
</head>
<script>

</script>
<body class="fixed-left" id="lxh-body">
<!-- Begin page -->
<div id="lxh-app">
    <!-- Top Bar Start -->
    <?php echo render_view('public.top-bar');?>
    <!-- Top Bar End -->

    <!-- ========== Left Sidebar Start ========== -->
    <?php echo render_view('public.left-sidebar');?>
    <!-- Left Sidebar End -->
    <script id="iframe-tpl" type="text/html">
        <div id="wrapper-{$name}" class="wrapper lxh-wrapper">
            <div class="content-page">
                <div class="content"><div class="container"><iframe src="{$url}" scrolling="no"></iframe></div></div>
            </div>
        </div>
    </script>
    <!-- END wrapper -->
    <?php
    echo load_css('toastr', 'lib/plugins/toastr');
    echo load_js('toastr.min', 'plugins/toastr');
    ?>
</div>

<footer class="footer text-right">
    <?php echo config('admin.copyright')?>
</footer>

<?php
//echo load_js('fastclick');
echo load_js('waves');
echo load_js('jquery.slimscroll');
?>

<script>
    var resizefunc = [];

    var $iframe = new Iframe(),
        $tab = new Tab($iframe)

    var $top = {
        tab: $tab,
        iframe: $iframe,
        homeUrl: '<?php echo $homeUrl?>'
    }

    // 加载首页视图
    $top.iframe.switch('home', '<?php echo $homeUrl?>');
</script>

<!-- KNOB JS -->
<!--[if IE]>
<?php //echo load_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
echo load_js('jquery.app');
?>

</body>
</html>
