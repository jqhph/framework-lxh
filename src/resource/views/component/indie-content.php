<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta name="description" content="<?php echo config('admin.desc');?>">
    <meta name="keywork" content="<?php echo config('admin.keywork');?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo load_img('favicon.ico'); ?>">
</head>
<body>
<?php
//echo load_css('bootstrap.min');
echo load_js('jquery.min');
echo load_js('util');
?>

<div class="content-wrapper">
    <?php if ($header || $description) {?>
        <section class="content-header"><h1><?php echo $header; ?><small><?php echo $description;?></small></h1></section>
    <?php }?>
    <section class="content"><?php echo $content;?></section>
</div>

<?php
echo render_view('public.app-js');
?>
<script>
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
    <?php
        echo $js;
        echo $css;
        echo $asyncJs;
        ?>;
    require_js('lib/plugins/toastr/toastr.min');
    require_css('lib/plugins/toastr/toastr.min.css');
    __then__(function () {<?php echo $script?>});
</script>
<?php
// 加载sea js，加载所有require_js和require_css加载的文件
echo load_js('app.min');
?>
</body>
</html>