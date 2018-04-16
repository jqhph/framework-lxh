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
    <?php echo $css;?>
</head>
<body class="lxh">
<?php
echo $loadscss;
if ($style) {?>
    <style><?php echo $style?></style>
<?php }

echo admin_js('js/jquery.min');
echo admin_js('js/util.min');
?>

<div class="content-wrapper">
    <?php if ($header || $description) {?>
        <section class="content-header"><h1><?php echo $header; ?><small><?php echo $description;?></small></h1></section>
    <?php }?>
    <section class="content"><?php echo $content;?></section>
</div>
<?php
// 初始化全局js变量
setup_admin_global_js_var();
?>
<script>
<?php if ($useDefaultAssets) {?>
require_js('@lxh/plugins/toastr/toastr.min');
require_css('@lxh/plugins/toastr/toastr.min');
<?php } ?>
<?php
    echo $js;
?>;
__then__(function () {<?php echo $script?>});
</script>
<?php
// app js初始化
setup_admin_js_app_ini();

// 加载sea js，加载所有require_js和require_css加载的文件
echo admin_js('js/app.min');

if ($hidden) {?><div style="display:none"><?php echo $hidden?></div><?php }?>
</body>
</html>