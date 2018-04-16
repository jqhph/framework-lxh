<?php

/**
 * 初始化后台全局js变量
 *
 */
function setup_admin_global_js_var()
{
?>
<script>
    // 全局变量容器，所有全局变量都应该放置到此容器，便于管理
    var LXHSTORE = {};
    LXHSTORE.loaderConfig = <?php echo json_encode(Lxh\Assets::loaderConfig())?>;
    LXHSTORE.ROUTEPREFIX = '<?php echo config('admin.route-prefix');?>';
    LXHSTORE.APIPREFIX = '/' + LXHSTORE.ROUTEPREFIX + '/api';
    LXHSTORE.cache = new Cache();
    LXHSTORE.cache.setToken('<?php
        // 设置缓存token，token刷新则会刷新所有缓存
        echo $GLOBALS['js-version'];
        ?>');
</script>
<?php
}

/**
 * app.js配置初始化
 *
 * @param bool $useDefaultAssets
 */
function setup_admin_js_app_ini($useDefaultAssets = true)
{
?>
<script>
    var __CONTROLLER__ = '<?php echo __CONTROLLER__?>', __ACTION__ = '<?php echo __ACTION__?>', __MODULE__ = '<?php echo __MODULE__;?>';
    <?php if ((isset($useDefaultAssets) && !empty($useDefaultAssets)) || !isset($useDefaultAssets)) {?>
    require_css([
        '@lxh/css/responsive.min',
        '@lxh/css/pages.min',
        '@lxh/css/components.min',
        '@lxh/css/icon.min',
        '@lxh/css/core.min'
    ]);
    require_js(['@lxh/js/container.min']);
    <?php } ?>
    // 配置
    function __ini__() {
        var data = {};
        // 容器配置
        data.options = <?php
        echo json_encode([
            'language' => config('language'),
            'js-version' => &$GLOBALS['js-version'],
            'css-version' => &$GLOBALS['css-version'],
            'use-cache' => config('client.loader.save'),
            'users' => [],
            'dataApi' => '/'.config('admin.route-prefix').'/api/js/data'
        ]);
        ?>
        // loader配置
        var publics = <?php echo json_encode(Lxh\Assets::publics())?>;
        // 需要载入的css
        data.publicCss = publics['public-js'];
        // 需要载入的js
        data.publicJs = publics['public-css'];
        <?php if ($langs = Lxh\Admin\Admin::getLangs()) {?>
        // 需要载入的语言包模块
        data.langScopes = <?php echo json_encode($langs);?>;
        <?php };?>
        return data
    }
</script>
<?php
}

/**
 * content页面全局变量初始化
 *
 */
function setup_admin_content_global_js_var()
{
    ?>
<script>
(function (w) {
    w.LXHSTORE = top.LXHSTORE;
    w.__then__ = top.__then__;
    w.require_js = top.require_js;
    w.require_css = top.require_css;
    w.to_under_score = top.to_under_score;
    w.build_http_params = top.build_http_params;
    w.lxhActions = (top.lxhActions = []);
    w.jsLibArr = (top.jsLibArr = []);
    w.cssLibArr = (top.cssLibArr = []);
    w.array_unique = top.array_unique;
    w.array_remove = top.array_remove;
    w.loading = top.loading;
    w.NProgress = top.NProgress;
    w.layer = window.layer || top.layer;
    w.window.formRules = [];
    w.open_tab = top.open_tab;
    w.close_tab = top.close_tab;
    w.reload_tab = top.reload_tab;
    w.back_tab = top.back_tab;
    document.onkeydown = function (e) {
        if (e.keyCode==116) {
            e.keyCode = 0;
            e.cancelBubble = true;
            w.LXHSTORE.IFRAME.reload();
            return false;
        }
    };

    w.LXHSTORE.VIEWKEY = '<?php echo Lxh\Admin\Grid::$viewKey?>';
})(window);
</script>
    <?php
}

/**
 * js模板
 *
 */
function admin_js_tpl()
{
?>
<script type="text/html" id="modal-tpl">
    <div id="{id}" class="modal fade {class}" >
        <div class="modal-dialog" style="width:{width};">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-action="modal-basic-close" class="close" data-dismiss="modal">×</button>
                    <h5 class="modal-title" >{title}</h5>
                </div>
                <div class="modal-body">{content}</div>
                <div class="modal-footer">
                    @foreach {buttons} {row}
                    <button data-action="{row.label}" type="button" class="btn {row.class} waves-effect" >{row.label}</button>
                    @endforeach
                    @if {dataId} || {useRefresh}
                    <button data-action="refresh" type="button" class="btn btn-purple waves-effect waves-light"><i class="zmdi zmdi-refresh-alt"></i> {refreshLabel}</button>
                    @endif
                    @if {confirmBtn}
                    <button data-action="confirm" type="button" class="btn {confirmBtnClass} waves-effect waves-light">{confirmBtnLabel}</button>
                    @endif
                    @if {closeBtn}
                    <button data-action="close" type="button" class="btn btn-default waves-effect" data-dismiss="modal">{closeBtnLabel}</button>
                    @endif

                    {footer}
                </div>
            </div>
        </div>
    </div>
</script>
<?php
}

