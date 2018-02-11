<script>
    var __CONTROLLER__ = '<?php echo __CONTROLLER__?>', __ACTION__ = '<?php echo __ACTION__?>', __MODULE__ = '<?php echo __MODULE__;?>';
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
    function __ini__() {
        window.APIPREFIX = '/admin/api/';
        var data = {};
        // 容器配置
        data.options = <?php
            $default = \Lxh\Assets::config();
            $client = config('client');
            $setting['sea-config'] = \Lxh\Helper\Util::merge($client['sea-config'], $default, true);
            $setting['language'] = config('language');
            $setting['js-version'] = & $GLOBALS['js-version'];
            $setting['css-version'] = & $GLOBALS['css-version'];
            //            $config['language-packages'] = language()->getPackages(['Global', __CONTROLLER__]);
            echo json_encode([
                'settings' => &$setting,
                'users' => admin()->all(),
                'dataApi' => '/admin/api/js/data'
            ]);
            ?>
            // seajs配置
            data.seaConfig = data.options.settings['sea-config'];
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