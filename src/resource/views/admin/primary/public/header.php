<script>
    (typeof $lxh == 'undefined') && ($lxh = parent.$lxh);
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
</script>
<?php
echo load_css('bootstrap.min');

//echo load_css('core');
//echo load_css('components');
?>