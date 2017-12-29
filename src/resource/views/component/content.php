<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo config('admin.title')?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<body>
<script>
    (typeof add_action == 'undefined') && (add_action = parent.add_action);
    (typeof add_js == 'undefined') && (add_js = parent.add_js);
    (typeof add_css == 'undefined') && (add_css = parent.add_css);
    (typeof to_under_score == 'undefined') && (to_under_score = parent.to_under_score);
    (typeof parse_view_name == 'undefined') && (parse_view_name = parent.parse_view_name);
    (typeof build_http_params == 'undefined') && (build_http_params = parent.build_http_params);
    (typeof lxhActions == 'undefined') && (lxhActions = (parent.lxhActions = []));
    (typeof jsLibArr == 'undefined') && (jsLibArr = (parent.jsLibArr = []));
    (typeof cssLibArr == 'undefined') && (cssLibArr = (parent.cssLibArr = []));
    (typeof array_unique == 'undefined') && (array_unique = parent.array_unique);
    (typeof loading == 'undefined') && (loading = parent.loading);
</script>

<?php if ($header || $description) {?>
<section class="content-header">
    <h1><?php echo $header; ?><small><?php echo $description;?></small></h1>
</section>
<?php }?>

<section class="content">
    <?php
    //    echo render_view('admin::partials.error');
    //    echo render_view('admin::partials.success');
    //    echo render_view('admin::partials.exception');
    //    echo render_view('admin::partials.toastr');
    ?>
    <?php echo $content;?>
</section>

<?php
echo load_css('bootstrap.min');
//echo load_js('util');
echo render_view('public.app-js');

echo $js;
echo $css;
?>
<script>
    (function (w) {
        w.tab = function () {
            var $top = w.$top || w.top.$top
            return $top.tab
        };
        w.open_tab = function (id, url, label) {
            tab().switch(id, url, label)
        };
        w.close_tab = function (id) {
            tab().close(id)
        };
        w.reload_tab = function (id, url, label) {
            tab().reload(id, url, label)
        };
        w.back_tab = function (step) {
            tab().back(step)
        };
    })(window);
    <?php echo $script?>
</script>
</body>
</html>