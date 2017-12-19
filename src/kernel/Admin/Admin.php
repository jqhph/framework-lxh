<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Auth\Database\Menu;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Navbar;
use Lxh\Database\Eloquent\Model as EloquentModel;
use Lxh\MVC\Model;
use Lxh\Support\Facades\Auth;
use Lxh\Support\Facades\Config;
use Lxh\Support\Facades\Route;
use InvalidArgumentException;

/**
 * Class Admin.
 */
class Admin
{
    /**
     * @var Navbar
     */
    protected $navbar;

    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

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
     * @param $model
     *
     * @return \Lxh\Admin\Form
     */
    public function form($model)
    {
        return new Form($this->getModel($model));
    }

    /**
     * Build a tree.
     *
     * @param $model
     *
     * @return \Lxh\Admin\Tree
     */
    public function tree($model, Closure $callable = null)
    {
        return new Tree($this->getModel($model), $callable);
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
     * Get namespace of controllers.
     *
     * @return string
     */
    public function controllerNamespace()
    {
        $directory = config('admin.directory');

        return ltrim(implode('\\',
              array_map('ucfirst',
                  explode(DIRECTORY_SEPARATOR, str_replace(__ROOT__, '', $directory)))), '\\')
              .'\\Controllers';
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
            self::$css = array_merge(self::$css, (array) $css);

            return;
        }

        $css = get_value(Form::collectFieldAssets(), 'css', []);

        static::$css = array_merge(static::$css, $css);

        $css = '';
        foreach (static::$css as &$css) {
            $css = load_css($css);
        }
        return $css;
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
            self::$js = array_merge(self::$js, (array) $js);

            return;
        }

        $js = get_value(Form::collectFieldAssets(), 'js', []);

        static::$js = array_merge(static::$js, $js);

        $js = '';
        foreach (static::$css as &$js) {
            $js = load_js($js);
        }
        return $js;
    }

    /**
     * @param string $script
     *
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
     * Admin url.
     *
     * @param $url
     *
     * @return string
     */
    public static function url($url)
    {
        $prefix = (string) config('admin.prefix');

        if (empty($prefix) || $prefix == '/') {
            return '/'.trim($url, '/');
        }

        return "/$prefix/".trim($url, '/');
    }

    /**
     * Left sider-bar menu.
     *
     * @return array
     */
    public function menu()
    {
        return (new Menu())->toTree();
    }

    /**
     * Get admin title.
     *
     * @return Config
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

    /**
     * Set navbar.
     *
     * @param Closure $builder
     */
    public function navbar(Closure $builder)
    {
        call_user_func($builder, $this->getNavbar());
    }

    /**
     * Get navbar object.
     *
     * @return \Lxh\Admin\Widgets\Navbar
     */
    public function getNavbar()
    {
        if (is_null($this->navbar)) {
            $this->navbar = new Navbar();
        }

        return $this->navbar;
    }

    public function registerAuthRoutes()
    {
    }

    public function registerHelpersRoutes($attributes = [])
    {
    }
}
