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
    echo admin_css('css/bootstrap.min');
    echo admin_css('css/menu-light.min');

    //    echo admin_css('components');
    echo admin_css('css/icon.min');
    
    echo admin_css('css/pages.min');

    echo admin_css('css/responsive.min');
    echo admin_css('css/components.min');
    echo admin_css('css/core.min');


    echo admin_js('js/util.min');
    echo admin_js('js/jquery.min');
    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="<?php echo ($collapse = config('admin.index.sitebar-collapse')) ? 'fixed-left-void' : ''?>" id="lxh-body">
<div id="wrapper" class="<?php echo $collapse ? 'forced enlarged' : ''?>">
    <div id="lxh-app">
        <?php
        // 输出顶部工具栏
        echo $topbar;
        // 输出左边导航栏
        echo $sitebar;

        echo admin_css('plugins/toastr/toastr.min');
        echo admin_js('plugins/toastr/toastr.min');
        ?>
    </div>

    <?php echo view('admin::index.right-bar')->render();?>
</div>

<footer class="footer text-right"><?php echo config('admin.copyright')?></footer>
<script id="iframe-tpl" type="text/html">
<div id="wrapper-{$name}" class="wrapper lxh-wrapper"><div class="content-page"><div class="content"></div></div></div>
</script>

<section class="content"><?php
    // 输出内外内容
    echo $content;
?></section>

<?php
// <!--    <iframe src="{$url}" scrolling="no"></iframe>-->
//echo admin_js('fastclick');
//echo admin_js('waves.min');
echo admin_js('js/jquery.slimscroll.min');
?>
<script>
    // 全局变量容器，所有全局变量都应该放置到此容器，便于管理
    var LXHSTORE = {};
    var resizefunc = [];
    LXHSTORE.IFRAME = new Iframe();
    LXHSTORE.TAB = new Tab(LXHSTORE.IFRAME);
    LXHSTORE.HOMEURL = '<?php echo $homeUrl?>';

    LXHSTORE.TAB.setMax(<?php echo $maxTab;?>);

    // 加载首页视图
    LXHSTORE.IFRAME.switch('home', LXHSTORE.HOMEURL);
    document.onkeydown = function (e) {
        if (e.keyCode==116) {
            e.keyCode = 0;
            e.cancelBubble = true;
            LXHSTORE.IFRAME.reload();
            return false;
        }
    };
    var open_tab = function (id, url, label) {LXHSTORE.TAB.switch(id, url, label)},
    close_tab = function (id) {LXHSTORE.TAB.close(id)},
    reload_tab = function (id, url, label) {LXHSTORE.TAB.reload(id, url, label)},
    back_tab = function (step) {LXHSTORE.TAB.back(step)};

    setTimeout(function () {
        console.log(LXHSTORE,window);
    }, 10000);
</script>

<!-- KNOB JS -->
<!--[if IE]>
<?php //echo admin_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
echo render_view('admin::index.app-js');

// <div id="toast-container" class="toast-top-right"><div class="toast toast-success" aria-live="polite" style="display: block;"><div class="toast-progress" style="width: 96.4454%;"></div><button type="button" class="toast-close-button" role="button">×</button><div class="toast-message">Login successful</div></div></div>
echo admin_js('js/jquery.app.min');

// 加载sea js，加载所有require_js和require_css加载的文件
echo admin_js('js/app.min');
// js模板
echo render_view('admin::javascript-tpl');
?>
</body>
</html>
