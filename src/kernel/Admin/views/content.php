<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php
    echo $css
    ?>
</head>
<body class="lxh" onmousewheel="top.document.body.scrollTop-=event.wheelDelta;">
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
    <?php
        echo $js;
        ?>; __then__(function(){<?php echo $script?>});
    $d.on('shown.bs.collapse', function () {LXHSTORE.IFRAME.height()});
    $d.on('pjax:complete', function () {$(parent.window).scrollTop(0);});
    var obj = window.top.document;
    function onMouseWheel(e) {
        e = e || window.event;
        if (e.type == "mousewheel") {
            delta = e.wheelDelta / 12;
        } else {
            delta = e.detail / 3 * -10;
        }
        if (chrome == -1) {
            obj.documentElement.scrollTop -= delta;
            if (e.preventDefault) {
                e.preventDefault();
            }
            return false;
        }
    }
    if (obj != null && obj != undefined) {
        var chrome = navigator.userAgent.search(/chrome/i),
            delta = 0;
        if (chrome != -1) {
            document.addEventListener("mousewheel", onMouseWheel, false);
        }
    }
})(window);
__then__(function () {
    $.pjax.defaults.timeout = 10000;
    $.pjax.defaults.maxCacheLength = 0;
    $(document).pjax('#pjax-container a:not(a[target="_blank"])', {container: '#pjax-container'});
    $(document).on('submit', 'form[pjax-container]', function(e) {$.pjax.submit(e, '#pjax-container')});
    $(document).on("pjax:popstate", function() {
        $(document).one("pjax:end", function(e) {
            $(e.target).find("script[data-exec-on-popstate]").each(function() {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        });
    });
    var $loading, $current = LXHSTORE.TAB.currentEl();
    $(document).on('pjax:send', function(xhr) {
        NProgress.start();
        $current = LXHSTORE.TAB.currentEl();
        if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
            var $submit_btn = $('form[pjax-container] :submit');
            if($submit_btn) $submit_btn.button('loading');
        }
        $loading = loading($('#pjax-container').parent());
    });
    $(document).on('pjax:complete', function(xhr) {
        NProgress.done();
        if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
            var $submit_btn = $('form[pjax-container] :submit');
            if($submit_btn) $submit_btn.button('reset');
        }
        $loading && $loading.close();
        // 重新绑定点击事件
        $('.grid-per-pager').change(change_pages);
        // 重新计算iframe高度
        LXHSTORE.IFRAME.height($current.iframe.find('iframe'));
    })
});
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
