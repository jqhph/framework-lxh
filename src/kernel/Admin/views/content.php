<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
</head>
<body class="lxh" onmousewheel="top.document.body.scrollTop-=event.wheelDelta">
<?php
// 初始化全局变量
setup_admin_content_global_js_var();
?>
<div class="container">
    <div class="content-wrapper">
        <?php if ($header || $description) {?>
        <section class="content-header"><h1><?php echo $header; ?><small> &nbsp;<?php echo $description;?></small></h1></section>
        <?php } else {
            echo '<div style="height:10px;"></div>';
        }?>
        <section class="content"><?php echo $content;?></section>
    </div>
</div>
<?php
echo $loadscss;
if ($style) {?>
    <style><?php echo $style?></style>
<?php
}
echo admin_js('js/jquery.min');
echo admin_js('js/bootstrap.min');
?>
<script>
(function(w){
    var $d = $(document);
    require_css('@lxh/css/bootstrap.min');
    <?php
        echo $js;
        echo $css;
        ?>; __then__(function(){<?php echo $script?>});
    $d.on('shown.bs.collapse', function () {LXHSTORE.IFRAME.height()});
    $d.on('pjax:complete', function () {$(parent.window).scrollTop(0);});
})(window);
</script>

<?php
setup_admin_js_app_ini();

echo $loadscripts;

echo admin_js('js/app.min');

if ($hidden) {?><div style="display:none"><?php echo $hidden?></div><?php }

admin_js_tpl();
?>

</body>
</html>
