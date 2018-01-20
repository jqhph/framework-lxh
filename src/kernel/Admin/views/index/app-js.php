<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 22:34
 */
//echo load_css('bootstrap.min');
//echo load_css('pages.min');
//echo load_css('core.min');
//echo load_css('components.min');
//echo load_css('icon.min');
echo load_js('jquery.min');
//echo load_js('bootstrap.min');
//echo load_js('detect');
//echo load_js('jquery.slimscroll.min');
//echo load_js('waves.min');

?>
<script>
    <?php if ((isset($useDefaultAssets) && !empty($useDefaultAssets)) || !isset($useDefaultAssets)) {?>
    require_css('css/bootstrap.min');
    require_css('css/pages.min');
    require_css('css/components.min');
    require_css('css/icon.min');
    require_css('css/core.min');
    require_js('lib/js/bootstrap.min');
    <?php } ?>
    // 配置
    function get_config() {
        window.APIPREFIX = '/admin/api/';
        var data = {};
        // 容器配置
        data.options = <?php
            $config = config('client-config');
            $config['language'] = config('language');
            $config['js-version'] = & $GLOBALS['js-version'];
            $config['css-version'] = & $GLOBALS['css-version'];
            //            $config['language-packages'] = language()->getPackages(['Global', __CONTROLLER__]);
            echo json_encode([
                'controller' => __CONTROLLER__,
                'module' => __MODULE__,
                'action' => __ACTION__,
                'config' => & $config,
                'users' => admin()->all(),
                'dataApi' => '/admin/api/js/data'
            ]);
            ?>
            // seajs配置
            data.seaConfig = data.options.config['sea-config'];
        // 需要载入的css
        data.publicCss = data.options.config['public-css'];
        // 需要载入的js
        data.publicJs = data.options.config['public-js'];

        <?php if ((isset($useLanguage) && !empty($useLanguage)) || !isset($useLanguage)) {?>
        // 需要载入的语言包模块
        data.langScopes = <?php echo json_encode(['Global', __CONTROLLER__]);?>
        <?php };?>

        return data
    }
</script>