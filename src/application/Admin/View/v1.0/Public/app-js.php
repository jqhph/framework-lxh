<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 22:34
 */

load_css('pages');
//load_css('responsive');

load_css('components');
load_css('icons');
load_css('core');

load_js('bootstrap.min');
load_js('fastclick');
load_js('detect');
load_js('jquery.slimscroll');

// 载入当前页js
if (! empty($loadCurrentJs)) {
    echo fetch_view(__ACTION__ . '-Js');
}
?>

<script>
    // 配置
    function get_config()
    {
        var config = {}
        // 容器配置
        config.options = <?php
            $config = (array) config('client-config');
            $config['language'] = config('language');
            $config['js-version'] = & $GLOBALS['js-version'];
            $config['css-version'] = & $GLOBALS['css-version'];
//            $config['language-packages'] = language()->getPackages(['Global', __CONTROLLER__]);

            echo json_encode([
                'controller' => __CONTROLLER__,
                'module' => __MODULE__,
                'action' => __ACTION__,
                'config' => & $config,
                'users' => user()->all()
            ]);
            ?>

        // seajs配置
        config.seaConfig = config.options.config['sea-config']
        // 需要载入的css
        config.publicCss = config.options.config['public-css']
        // 需要载入的js
        config.publicJs = config.options.config['public-js']
        // 需要载入的语言包模块
        config.langScopes = <?php echo json_encode(['Global', __CONTROLLER__]);?>

//        config.tplnames = <?php //echo json_encode(['component.fields.int.int', 'component.fields.string.string']);?>

        return config
    }
</script>
<?php
load_js('app');
?>