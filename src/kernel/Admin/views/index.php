<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo config('admin.description')?>">
    <meta name="keywords" content="<?php echo config('admin.keywords')?>">
    <meta name="author" content="<?php echo config('admin.author')?>">
    <link rel="shortcut icon" href="<?php echo config('admin.favicon');?>">
    <title><?php echo config('admin.title'); ?></title>

    <?php
    // App css
    echo load_css('bootstrap.min');
    echo load_css('menu-light.min');

    //    echo load_css('components');
    echo load_css('icon.min');
    echo load_css('core.min');

    echo load_js('util.min');
    echo load_js('jquery.min');
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
<body class="fixed-left" id="lxh-body">
<div id="wrapper">
    <div id="lxh-app">
        <?php
        // 输出顶部工具栏
        echo $topbar;
        // 输出左边导航栏
        echo $sitebar;

        echo load_css('toastr.min', 'lib/plugins/toastr');
        echo load_js('toastr.min', 'plugins/toastr');
        ?>
    </div>
</div>

<footer class="footer text-right"><?php echo config('admin.copyright')?></footer>
<script id="iframe-tpl" type="text/html">
    <div id="wrapper-{$name}" class="wrapper lxh-wrapper">
        <div class="content-page">
            <div class="content"><div class="container"><iframe src="{$url}" scrolling="no"></iframe></div></div>
        </div>
    </div>
</script>

<section class="content"><?php
    // 输出内外内容
    echo $content;
?></section>

<?php
//echo load_js('fastclick');
//echo load_js('waves.min');
echo load_js('jquery.slimscroll.min');
?>
<script>
    var resizefunc = [];
    var IFRAME = new Iframe(),
        TAB = new Tab(IFRAME),
        HOMEURL = '<?php echo $homeUrl?>';

    TAB.setMax(<?php echo $maxTab;?>);

    // 加载首页视图
    IFRAME.switch('home', HOMEURL);
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
