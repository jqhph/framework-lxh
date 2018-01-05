<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo config('admin.title')?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<body>
<script>
    __then__ = parent.__then__;
    require_js = parent.require_js;
    require_css = parent.require_css;
    to_under_score = parent.to_under_score;
    parse_view_name = parent.parse_view_name;
    build_http_params = parent.build_http_params;
    lxhActions = (parent.lxhActions = []);
    jsLibArr = (parent.jsLibArr = []);
    cssLibArr = (parent.cssLibArr = []);
    array_unique = parent.array_unique;
</script>

<div class="content-wrapper">
<?php if ($header || $description) {?>
<section class="content-header"><h1><?php echo $header; ?><small><?php echo $description;?></small></h1></section>
<?php } else {
    echo '<div style="height:10px;"></div>';
}?>
<section class="content"><?php echo $content;?></section>
</div>

<?php
echo render_view('public.app-js');
?>
<script>
(function (w) {
    w.TAB = w.top.TAB;
    w.IFRAME = w.top.IFRAME;;
    w.open_tab = function (id, url, label) {
        TAB.switch(id, url, label)
    };
    w.close_tab = function (id) {
        TAB.close(id)
    };
    w.reload_tab = function (id, url, label) {
        TAB.reload(id, url, label)
    };
    w.back_tab = function (step) {
        TAB.back(step)
    };
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
__then__(function () {<?php echo $script?>});
</script>
<?php
// 加载sea js，加载所有require_js和require_css加载的文件
echo load_js('app.min');
?>
</body>
</html>