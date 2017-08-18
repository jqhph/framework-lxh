<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 22:34
 */
// <!--Morris Chart CSS -->
//    echo load_css('morris', 'plugins/morris');

// App css
echo load_css('bootstrap.min');
echo load_css('pages');
echo load_css('menu');
echo load_css('responsive');

//    echo load_js('modernizr.min');

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
//                'controller' => __CONTROLLER__,
                'module' => __MODULE__,
//                'action' => __ACTION__,
                'config' => & $config,
            ]);
            ?>
            // seajs配置
            config.seaConfig = config.options.config['sea-config']
            // 需要载入的css
            config.publicCss = config.options.config['public-css']
            // 需要载入的js
            config.publicJs = config.options.config['public-js']
            // 需要载入的语言包模块
//            config.langScopes = <?php //echo json_encode(['Global', __CONTROLLER__]);?>
            // 需要载入的模板
//            config.tplnames = <?php //echo json_encode(['component.fields.int.int', 'component.fields.string.string']);?>

        return config
    }
</script>
<?php
echo load_js('router');
echo load_js('app');
?>