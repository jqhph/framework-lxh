<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo config('admin.title')?></title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<body>
<script>
    LXHSTORE = parent.LXHSTORE;
    __then__ = parent.__then__;
    require_js = parent.require_js;
    require_css = parent.require_css;
    to_under_score = parent.to_under_score;
    build_http_params = parent.build_http_params;
    lxhActions = (parent.lxhActions = []);
    jsLibArr = (parent.jsLibArr = []);
    cssLibArr = (parent.cssLibArr = []);
    array_unique = parent.array_unique;
    loading = parent.loading;
    NProgress = parent.NProgress;
    window.formRules = [];
    open_tab = parent.open_tab;
    close_tab = parent.close_tab;
    reload_tab = parent.reload_tab;
    back_tab = parent.back_tab;
    var __CONTROLLER__ = '<?php echo __CONTROLLER__?>',
        __ACTION__ = '<?php echo __ACTION__?>',
        __MODULE__ = '<?php echo __MODULE__;?>';
    document.onkeydown = function (e) {
        if (e.keyCode==116) {
            e.keyCode = 0;
            e.cancelBubble = true;
            LXHSTORE.IFRAME.reload();
            return false;
        }
    }
</script>
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
echo admin_js('js/jquery.min');
echo render_view('admin::index.app-js');
?>
<script>
<?php
    echo $js;
    echo $css;
    echo $asyncJs;
?>; __then__(function(){<?php echo $script?>});
</script>
<?php
echo admin_js('js/app.min');
?>