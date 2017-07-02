<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 22:34
 */
load_css('bootstrap.min');
load_css('pages');
load_js('sea');

// 要载入的css
$css = [

];

// 要载入的js
$js = [

];
?>
<script>
    // 配置
     var config = <?php echo json_encode(config('sea-config')); ?>,
         publicCss = <?php echo json_encode(config('public-css')); ?>,
         publicJs = <?php echo json_encode(config('public-js')); ?>

    seajs.config(config);
    // 加载css
    seajs.use(publicCss)
</script>
<?php
    // 载入当前页js
    echo fetch_view(__ACTION__ . '-Js');
?>
<script>
    // 优先加载jquery
    seajs.use('jquery', function (q) {
        seajs.use(publicJs, function () {
            var plugIns = arguments // 所有加载进来的js插件变量数组
            init(function () {
                if (typeof LxhAction == 'function') {
                    // 运行当前页js
                    LxhAction(plugIns)
                }
            })

        })
    })

    // 初始化
    function init(call) {
        window.$lxh = new Lxh(<?php
            $config = (array) config('client-config');
            $config['language'] = config('language');

            echo json_encode([
                'controller' => __CONTROLLER__,
                'module' => __MODULE__,
                'action' => __ACTION__,
                'config' => $config,

        ])?>)

        // 语言包设置
        $lxh.language.type($lxh.config.get('language'))
        // 载入需要的语言包
        $lxh.language.fetch(<?php echo json_encode(['Global', __CONTROLLER__])?>, call)
    }
</script>