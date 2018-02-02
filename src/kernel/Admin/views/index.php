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
    echo admin_css('css/core.min');
    echo admin_css('css/components.min');

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
<style>#nprogress{pointer-events:none}#nprogress .bar{background:#f4645f;position:fixed;z-index:1031;top:53px;left:0;width:100%;height:2px}#nprogress .peg{display:block;position:absolute;right:0;width:100px;height:0;box-shadow:0 0 10px #f4645f,0 0 5px #f4645f;opacity:1;-webkit-transform:rotate(3deg) translate(0,-4px);-ms-transform:rotate(3deg) translate(0,-4px);transform:rotate(3deg) translate(0,-4px)}#nprogress .spinner{display:block;position:fixed;z-index:1031;top:68px;right:15px}#nprogress .spinner-icon{width:18px;height:18px;box-sizing:border-box;border:solid 2px transparent;border-top-color:#f4645f;border-left-color:#f4645f;border-radius:50%;-webkit-animation:nprogress-spinner .4s linear infinite;animation:nprogress-spinner .4s linear infinite}.nprogress-custom-parent{overflow:hidden;position:relative}.nprogress-custom-parent #nprogress .bar,.nprogress-custom-parent #nprogress .spinner{position:absolute}@-webkit-keyframes nprogress-spinner{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}@keyframes nprogress-spinner{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>

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
    <div id="wrapper-{$name}" class="wrapper lxh-wrapper">
        <div class="content-page">
            <div class="content"><iframe src="{$url}" scrolling="no"></iframe></div>
        </div>
    </div>
</script>

<section class="content"><?php
    // 输出内外内容
    echo $content;
?></section>

<?php
//echo admin_js('fastclick');
//echo admin_js('waves.min');
echo admin_js('js/jquery.slimscroll.min');
echo admin_js('plugins/nprogress/nprogress.min');
?>
<script>
    var resizefunc = [];
    var IFRAME = new Iframe(),
        TAB = new Tab(IFRAME),
        HOMEURL = '<?php echo $homeUrl?>';

    TAB.setMax(<?php echo $maxTab;?>);

    // 加载首页视图
    IFRAME.switch('home', HOMEURL);
    document.onkeydown = function (e) {
        if (e.keyCode==116) {
            e.keyCode = 0;
            e.cancelBubble = true;
            IFRAME.reload();
            return false;
        }
    };
    var open_tab = function (id, url, label) {TAB.switch(id, url, label)},
    close_tab = function (id) {TAB.close(id)},
    reload_tab = function (id, url, label) {TAB.reload(id, url, label)},
    back_tab = function (step) {TAB.back(step)};
    $(document).on('iframe.creating',function(){NProgress.start();});
    $(document).on('iframe.created',function(){NProgress.done();});
</script>

<!-- KNOB JS -->
<!--[if IE]>
<?php //echo admin_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
// <div id="toast-container" class="toast-top-right"><div class="toast toast-success" aria-live="polite" style="display: block;"><div class="toast-progress" style="width: 96.4454%;"></div><button type="button" class="toast-close-button" role="button">×</button><div class="toast-message">Login successful</div></div></div>
echo admin_js('js/jquery.app.min');
?>
</body>
</html>
