<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Navbar;
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
     * @var array
     */
    public static $asyncjs = [];

    /**
     * @var array
     */
    public static $loadScripts = [];

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
     * @param $model
     * @param Closure $callable
     *
     * @return \Lxh\Admin\Grid
     */
    public function grid($model, Closure $callable)
    {
        return new Grid($this->getModel($model), $callable);
    }

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
     * @param $model
     *
     * @return \Lxh\Admin\Form
     */
    public function form($model)
    {
        return new Form($this->getModel($model));
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

    /**
     * Add css or get all css.
     *
     * @param null $css
     *
     * @return array
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            self::$css = array_merge(self::$css, (array)$css);
            return;
        }

        $script = '';
        foreach (array_unique(static::$css) as &$css) {
            $script .= "require_css('$css.css');";
        }
        return $script;
    }


    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return array
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            self::$js = array_merge(self::$js, (array)$js);
            return;
        }

        $script = '';
        foreach (array_unique(static::$js) as &$js) {
            $script .= "require_js('$js');";
        }
        return $script;
    }

    /**
     * 同步载入js
     *
     * @param $src
     */
    public static function loadScript($src)
    {
        static::$loadScripts[] = &$src;
    }

    /**
     *
     * @return string
     */
    public static function getLoadScripts()
    {
        $html = '';
        foreach (array_unique(static::$loadScripts) as &$src) {
            $html .= "<script src='{$src}'></script>";
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
     * 使用js异步加载代码
     *
     * @param null $js
     * @return string|void
     */
    public static function async($js = null)
    {
        if (!is_null($js)) {
            self::$asyncjs = array_merge(self::$asyncjs, (array) $js);
            return;
        }

        $script = '';
        foreach (static::$asyncjs as &$js) {
            $script .= "require_js('$js')";
        }
        return $script;
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
        return admin();
    }

}
