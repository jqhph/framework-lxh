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
    echo admin_css('css/bootstrap.min');
    echo admin_css('css/menu-light.min');
    echo admin_css('css/core.min');
    echo admin_css('css/components.min');

    echo admin_js('js/jquery.min');
    echo admin_js('js/util.min');
    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="<?php echo (true) ? 'fixed-left-void' : ''?>" id="lxh-body">
<div id="wrapper" class="<?php echo true ? 'forced enlarged' : ''?>">
    <div id="lxh-app">
        <?php
        // 输出顶部工具栏
        echo $topbar;
        // 输出左边导航栏
        echo $sitebar;
        ?>
    </div>

    <?php echo view('admin::index.right-bar')->render();?>
</div>

<footer class="footer text-right"><?php echo config('admin.copyright')?></footer>
<script id="iframe-tpl" type="text/html">
<div id="wrapper-{$name}" class="wrapper lxh-wrapper"><div class="content-page"><div class="content"><iframe src="{$url}" scrolling="no"></iframe></div></div></div>
</script>

<section class="content"><?php
    // 输出内外内容
    echo $content;
?></section>

<?php
// 初始化全局js变量
setup_admin_global_js_var();
?>
<script>
    LXHSTORE.HOMEURL = '/' + LXHSTORE.ROUTEPREFIX + '<?php echo $homeUrl?>';
    LXHSTORE.IFRAME = new Iframe();
    LXHSTORE.TAB = new Tab(LXHSTORE.IFRAME);

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

    require_css([
        '@lxh/css/icon.min',
        '@lxh/plugins/toastr/toastr.min'
    ]);
    require_js([
        '@lxh/js/jquery.app.min',
        '@lxh/plugins/toastr/toastr.min',
        '@lxh/js/bootstrap.min',
        '@lxh/js/jquery.slimscroll.min'
    ]);
</script>

<?php
setup_admin_js_app_ini();

echo admin_js('js/app.min');
echo admin_js('packages/layer/layer');
// 快捷菜单
echo view('admin::index.context-menu', ['contextMenus' => &$contextMenus])->render();
?>
<div class="fixed-bottom-btn"><a id="return-top">TOP <i class="zmdi zmdi-long-arrow-up"></i></a></div>
<script>
    layer.config({maxmin:true,moveOut:true});
</script>
</body>
</html>
