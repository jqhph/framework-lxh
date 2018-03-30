<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Navbar;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;
use Lxh\MVC\Model;
use InvalidArgumentException;

/**
 * Class Admin.
 */
class Admin
{
    /**
     * @var array
     */
    protected static $urls = [];

    /**
     * @var string
     */
    protected static $scope = __CONTROLLER__;

    protected static $html = '';

    /**
     * 前端加载的语言包
     *
     * @var array
     */
    protected static $langs = [];

    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $style = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * 同步载入的js
     *
     * @var array
     */
    public static $loadScripts = [];

    /**
     * 同步载入的css
     *
     * @var array
     */
    public static $loadStyles = [];

    /**
     * @var array
     */
    protected static $assetsClass = [];

    /**
     * @var array
     */
    protected static $scriptClass = [];

    /**
     * id字段名
     *
     * @var string
     */
    protected static $idName = '';

    /**
     * 是否已加载帮助函数文件
     *
     * @var bool
     */
    protected static $loadedHelpers = false;

    /**
     * 后台首页内容
     *
     * @return Index
     */
    public function index(Closure $callable = null)
    {
        return new Index($callable);
    }

    /**
     * 设置或获取模型名称
     *
     * @param null $scope
     * @return string
     */
    public static function model($scope = null)
    {
        if ($scope) {
            static::$scope = $scope;
            default_model_name($scope);
        }

        return static::$scope;
    }

    /**
     * 加载帮助函数
     *
     * @return void
     */
    public static function includeHelpers()
    {
        if (static::$loadedHelpers) return;

        static::$loadedHelpers = true;

        include __ROOT__ . 'kernel/Admin/Support/helpers.php';
    }


    /**
     * 设置或获取id名称
     *
     * @param null $scope
     * @return string
     */
    public static function id($id = null)
    {
        if ($id) {
            static::$idName = $id;
        }
        if (empty(static::$idName)) {
            static::$idName = model(static::model())->getKeyName();
        }

        return static::$idName;
    }

    /**
     * Admin url.
     *
     * @return Url
     */
    public static function url($scope = null)
    {
        $scope = $scope ?: __CONTROLLER__;

        return isset(static::$urls[$scope]) ? static::$urls[$scope] : (static::$urls[$scope] = Url::create($scope));
    }

    /**
     * @param Closure $callable
     *
     * @return \Lxh\Admin\Layout\Content
     */
    public function content(Closure $callable = null)
    {
        return new Content($callable);
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function getModel($model)
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (is_string($model)) {
            return model($model);
        }

        throw new InvalidArgumentException("$model is not a valid model");
    }

    public static function hidden($html = null)
    {
        if (!$html) {
            return static::$html;
        }
        static::$html .= $html;
    }

    /**
     * Add css or get all css.
     *
     * @param string|array $css
     *
     * @return null|string
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            self::$css = array_merge(self::$css, (array)$css);
            return;
        }

        $styles = json_encode(static::$css);

        return "require_css($styles);";
    }


    /**
     * Add js or get all js.
     *
     * @param string|array $js
     *
     * @return null|string
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            self::$js = array_merge(self::$js, (array)$js);
            return;
        }

        $script = json_encode(static::$js);

        return "require_js($script);";
    }

    /**
     * 同步载入js
     *
     * @param $src
     */
    public static function loadScript($src)
    {
        if (is_array($src)) {
            static::$loadScripts = array_merge(static::$loadScripts, $src);
        } else {
            static::$loadScripts[] = &$src;
        }
    }

    /**
     * 渲染视图的同时加载静态资源
     *
     * @param $content
     */
    public static function loadAssets($content)
    {
        if ($content instanceof Renderable) {
            $content = $content->render();
        }

        $jsv  = $GLOBALS['js-version'];
        $cssv = $GLOBALS['css-version'];

        static::collectFieldAssets();
        $js      = static::js();
        $css     = static::css();
        $script  = static::script();
        $syncJs  = static::getLoadScripts();
        $syncCss = static::getLoadStyles();

        ob_start();

        echo "{$syncCss}{$syncJs}<script>{$css}{$js}__then__(function(){ $script });</script>$content";
        ?>
<script>
(function () {
    // 加载css
    new LxhLoader(get_used_css([], <?php echo $cssv?>)).request();
    new LxhLoader(get_used_js([], <?php echo $jsv?>), call_actions).request();
})();
</script>
        <?php

        return ob_get_clean();
    }

    /**
     *
     * @return string
     */
    public static function getLoadScripts()
    {
        $server = config('client.resource-server');

        $html = '';
        foreach (array_unique(static::$loadScripts) as &$src) {
            if (strpos($src, '//') === false) {
                $src = $server . $src;
            }

            $html .= "<script src='{$src}'></script>";
        }
        return $html;
    }

    /**
     * 同步载入js
     *
     * @param $css
     */
    public static function loadStyle($css)
    {
        if (is_array($css)) {
            static::$loadStyles = array_merge(static::$loadStyles, $css);
        } else {
            static::$loadStyles[] = &$src;
        }
    }

    /**
     *
     * @return string
     */
    public static function getLoadStyles()
    {
        $server = config('client.resource-server');

        $html = '';
        foreach (array_unique(static::$loadStyles) as &$css) {
            if (strpos($css, '//') === false) {
                $css = $server . $css;
            }

            $html .= "<link href='{$css}' rel=\"stylesheet\" type=\"text/css\" />";
        }
        return $html;
    }

    /**
     * 禁用语言包
     *
     * @return void
     */
    public static function disableLang()
    {
        static::$langs = false;
    }

    /**
     * 设置语言包
     *
     * @param array $langs
     */
    public static function setLangs(array $langs)
    {
        static::$langs = &$langs;
    }

    /**
     * 获取语言包
     *
     * @param array $langs
     * @return array
     */
    public static function getLangs()
    {
        if (static::$langs !== false && empty(static::$langs)) {
            // 设置默认语言包
            static::$langs = ['Global', __CONTROLLER__];
        }

        return static::$langs;
    }

    /**
     * @param string $script
     * @return array
     */
    public static function script($script = '')
    {
        if (!empty($script)) {
            self::$script = array_merge(self::$script, (array) $script);
            return;
        }

        return implode(';', static::$script);
    }

    /**
     *
     * @param null $css
     * @return array
     */
    public static function style($style = null)
    {
        if (!is_null($style)) {
            self::$style = array_merge(self::$style, (array) $style);
            return;
        }

        return implode('', self::$style);
    }

    public static function collectFieldAssets()
    {
        foreach (static::$assetsClass as $class => &$v) {
            $assets = $class::getAssets();

            static::js($assets['js']);
            static::css($assets['css']);
        }

        foreach (static::$scriptClass as $class => &$v) {
            static::script($class::$scripts);
        }
    }

    /**
     * 注册全局只加载一次JS或CSS的类
     *
     * @param $class
     */
    public static function addAssetsFieldClass($class)
    {
        static::$assetsClass[$class] = 1;
    }

    /**
     * 注册全局只加载一次的JS代码的类
     *
     * @param $class
     */
    public static function addScriptClass($class)
    {
        static::$scriptClass[$class] = 1;
    }

    /**
     * Get admin title.
     *
     * @return string
     */
    public function title()
    {
        return config('admin.title');
    }

    /**
     * Get current login user.
     *
     * @return mixed
     */
    public function user()
    {
        return __admin__();
    }

}
