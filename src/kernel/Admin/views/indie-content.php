<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta name="description" content="<?php echo config('admin.description');?>">
    <meta name="keywords" content="<?php echo config('admin.keywords');?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo config('admin.favicon'); ?>">
</head>
<body class="lxh">
<?php
//echo admin_css('css/bootstrap.min');
//echo admin_css('css/core.min');
//echo admin_css('css/pages.min');
//echo admin_css('css/components.min');
echo admin_js('js/jquery.min');
echo admin_js('js/util.min');
?>

<div class="content-wrapper">
    <?php if ($header || $description) {?>
        <section class="content-header"><h1><?php echo $header; ?><small><?php echo $description;?></small></h1></section>
    <?php }?>
    <section class="content"><?php echo $content;?></section>
</div>

<script>
    var LXHSTORE = {};
    LXHSTORE.loaderConfig = <?php echo json_encode(Lxh\Assets::loaderConfig())?>;
    LXHSTORE.cache = new Cache();
    LXHSTORE.cache.setToken('<?php
        // 设置缓存token，token刷新则会刷新所有缓存
        echo $GLOBALS['js-version'];
        ?>');
    (function (w) {
        w.loading = function (el, circle, timeout) {
            el = el || 'body';
            function loading() {
                var $el = typeof el == 'object' ? el : $(el);
                if (circle) {
                    $el.append('<div class=" loading loading-circle"></div>')
                } else {
                    $el.append('<div class=" loading"><div class="loading1"></div><div class="loading2"></div><div class="loading3"></div></div>')
                }
                this.close = function () {$el.find('.loading').remove()};
                if (timeout) setTimeout(this.close, timeout);
            }
            return new loading();
        }
    })(window);
    require_css('@lxh/css/bootstrap.min');
    <?php
        echo $js;
        echo $css;
        ?>;
    require_js('@lxh/plugins/toastr/toastr.min');
    require_css('@lxh/plugins/toastr/toastr.min');
    __then__(function () {<?php echo $script?>});
</script>
<?php
echo view('admin::index.app-js')->render();

// 加载sea js，加载所有require_js和require_css加载的文件
echo admin_js('js/app.min');
?>
<?php if ($hidden) {?><div style="display:none"><?php echo $hidden?></div><?php }?>
</body>
</html>