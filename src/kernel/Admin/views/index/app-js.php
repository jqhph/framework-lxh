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
echo admin_js('js/jquery.min');
//echo load_js('bootstrap.min');
//echo load_js('detect');
//echo load_js('jquery.slimscroll.min');
//echo load_js('waves.min');

?>
<script>
    <?php if ((isset($useDefaultAssets) && !empty($useDefaultAssets)) || !isset($useDefaultAssets)) {?>
    require_css('@lxh/css/responsive.min');
    require_css('@lxh/css/bootstrap.min');
    require_css('@lxh/css/pages.min');
    require_css('@lxh/css/components.min');
    require_css('@lxh/css/icon.min');
    require_css('@lxh/css/core.min');
    require_js('@lxh/js/bootstrap.min');
    <?php } ?>
    // 配置
    function get_config() {
        window.APIPREFIX = '/admin/api/';
        var data = {};
        // 容器配置
        data.options = <?php
            $default = \Lxh\Assets::config();
            $client = config('client');
            $config['sea-config'] = \Lxh\Helper\Util::merge($client['sea-config'], $default, true);
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
            data.seaConfig.alias = data.seaConfig.alias || [];
            var publics = <?php echo json_encode(\Lxh\Assets::publics())?>;

        // 需要载入的css
        data.publicCss = publics['public-js'];
        // 需要载入的js
        data.publicJs = publics['public-css'];

        <?php if ($langs = \Lxh\Admin\Admin::getLangs()) {?>
        // 需要载入的语言包模块
        data.langScopes = <?php echo json_encode($langs);?>
        <?php };?>

        return data
    }
</script>