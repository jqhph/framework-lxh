<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 22:34
 */

echo load_css('pages.min');
echo load_css('core.min');
echo load_css('components.min');
echo load_css('icon.min');
echo load_js('jquery.min');
echo load_js('bootstrap.min');
//echo load_js('fastclick');
//echo load_js('detect');
//echo load_js('jquery.slimscroll.min');
echo load_js('waves.min');

?>

<script>
    // 配置
    function get_config() {
        var data = {}
        // 容器配置
        data.options = <?php
            $config = array_merge(config('client-config'), config('replica-client-config'));
            $config['language'] = config('language');
            $config['js-version'] = & $GLOBALS['js-version'];
            $config['css-version'] = & $GLOBALS['css-version'];
//            $config['language-packages'] = language()->getPackages(['Global', __CONTROLLER__]);

            echo json_encode([
                'controller' => __CONTROLLER__,
                'module' => __MODULE__,
                'action' => __ACTION__,
                'config' => & $config,
                'users' => admin()->all()
            ]);
            ?>
            
        // seajs配置
        data.seaConfig = data.options.config['sea-config']
        // 需要载入的css
        data.publicCss = data.options.config['public-css']
        // 需要载入的js
        data.publicJs = data.options.config['public-js']
        // 需要载入的语言包模块
        data.langScopes = <?php echo json_encode(['Global', __CONTROLLER__]);?>

//        config.tplnames = <?php //echo json_encode(['component.fields.int.int', 'component.fields.string.string']);?>

        return data
    }
</script>
<?php
echo load_js('app');
?>