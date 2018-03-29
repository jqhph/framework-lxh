<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
</head>
<body class="lxh" onmousewheel="top.document.body.scrollTop-=event.wheelDelta">
<script>
(function (w) {
    w.LXHSTORE = parent.LXHSTORE;
    w.__then__ = parent.__then__;
    w.require_js = parent.require_js;
    w.require_css = parent.require_css;
    w.to_under_score = parent.to_under_score;
    w.build_http_params = parent.build_http_params;
    w.lxhActions = (parent.lxhActions = []);
    w.jsLibArr = (parent.jsLibArr = []);
    w.cssLibArr = (parent.cssLibArr = []);
    w.array_unique = parent.array_unique;
    w.array_remove = parent.array_remove;
    w.loading = parent.loading;
    w.NProgress = parent.NProgress;
    w.layer = window.layer || parent.layer;
    w.window.formRules = [];
    w.open_tab = parent.open_tab;
    w.close_tab = parent.close_tab;
    w.reload_tab = parent.reload_tab;
    w.back_tab = parent.back_tab;
    document.onkeydown = function (e) {
        if (e.keyCode==116) {
            e.keyCode = 0;
            e.cancelBubble = true;
            w.LXHSTORE.IFRAME.reload();
            return false;
        }
    };

    w.LXHSTORE.VIEWKEY = '<?php echo Lxh\Admin\Grid::$viewKey?>';
})(window);
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
echo $loadscss;
if ($style) {?>
    <style><?php echo $style?></style>
<?php } ?>
<?php 
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
echo view('admin::index.app-js')->render();
echo $loadscripts;

echo admin_js('js/app.min');
?>
<?php if ($hidden) {?><div style="display:none"><?php echo $hidden?></div><?php }?>
<script type="text/html" id="modal-tpl">
    <div id="{id}" class="modal fade {class}" >
        <div class="modal-dialog" style="width:{width};">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-action="modal-basic-close" class="close" data-dismiss="modal">×</button>
                    <h5 class="modal-title" >{title}</h5>
                </div>
                <div class="modal-body">{content}</div>
                <div class="modal-footer">
                    @foreach {buttons} {row}
                    <button data-action="{row.label}" type="button" class="btn {row.class} waves-effect" >{row.label}</button>
                    @endforeach
                    @if {dataId} || {useRefresh}
                    <button data-action="refresh" type="button" class="btn btn-purple waves-effect waves-light"><i class="zmdi zmdi-refresh-alt"></i> {refreshLabel}</button>
                    @endif
                    @if {confirmBtn}
                    <button data-action="confirm" type="button" class="btn {confirmBtnClass} waves-effect waves-light">{confirmBtnLabel}</button>
                    @endif
                    @if {closeBtn}
                    <button data-action="close" type="button" class="btn btn-default waves-effect" data-dismiss="modal">{closeBtnLabel}</button>
                    @endif

                    {footer}
                </div>
            </div>
        </div>
    </div>
</script>
</body>
</html>
