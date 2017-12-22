<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<style>.loading{position:absolute;top:50%;left:48.5%;margin-top:-30px;z-index:999999;width:100px;text-align:center}.loading>div{width:20px;height:20px;background-color:#36d5ac;border-radius:100%;display:inline-block;-webkit-animation:bouncedelay 1.4s infinite ease-in-out;animation:bouncedelay 1.4s infinite ease-in-out;-webkit-animation-fill-mode:both;animation-fill-mode:both}.loading .loading1{-webkit-animation-delay:-.32s;animation-delay:-.32s}.loading .loading2{-webkit-animation-delay:-.16s;animation-delay:-.16s}.loading-circle{position:relative;display:inline-block;line-height:0;vertical-align:middle;box-sizing:border-box}.loading-circle::after{content:"";display:inline-block;border:3px solid rgba(0,0,0,.1);border-radius:50%;height:32px;width:32px;box-sizing:border-box}.loading-circle:after{position:absolute;left:0;top:0;border-color:#21d376 transparent transparent;animation:loading .6s linear infinite}.loading-circle-sm::after,.loading-circle-sm::before{height:18px;width:18px;border-width:2px}.loading-circle-xs::after,.loading-circle-xs::before{height:12px;width:12px;border-width:2px}@keyframes loading{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>

<body>
<script>
    (typeof $lxh == 'undefined') && ($lxh = parent.$lxh);
    (typeof add_action == 'undefined') && (add_action = parent.add_action);
    (typeof add_js == 'undefined') && (add_js = parent.add_js);
    (typeof add_css == 'undefined') && (add_css = parent.add_css);
    (typeof to_under_score == 'undefined') && (to_under_score = parent.to_under_score);
    (typeof parse_view_name == 'undefined') && (parse_view_name = parent.parse_view_name);
    (typeof build_http_params == 'undefined') && (build_http_params = parent.build_http_params);
    (typeof lxhActions == 'undefined') && (lxhActions = parent.lxhActions);
    (typeof jsLibArr == 'undefined') && (jsLibArr = parent.jsLibArr);
    (typeof cssLibArr == 'undefined') && (cssLibArr = parent.cssLibArr);
    (typeof array_unique == 'undefined') && (array_unique = parent.array_unique);
</script>

<section class="content-header">
    <h1>
        <?php echo $header; ?>
        <small><?php echo $description;?></small>
    </h1>

</section>

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

?>
<script>
    (function (w) {
        w.tab = function () {
            var $top = w.$top || w.top.$top
            return $top.tab
        }

        w.open_tab = function (id, url, label) {
            tab().switch(id, url, label)
        }
    })(window)
</script>
</body>
</html>