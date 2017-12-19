<?php
/**
 * 模板管理
 *
 * @author Jqh
 * @date   2017/6/15 14:53
 */

namespace Lxh\Template;

use Lxh\Exceptions\Error;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Entity;
use \Lxh\Helper\Util;
use Lxh\MVC\ControllerManager;

class View
{
    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * 模板变量管理对象
     *
     * @var Entity
     */
    protected $vars = [];

    /**
     * The array of views that have been located.
     *
     * @var array
     */
    protected $views = [];

    /**
     * The namespace to file path hints.
     *
     * @var array
     */
    protected $hints = [];

    // 模板版本
    protected $version;

    protected $root = __ROOT__;

    protected $dir = '';

    protected $currentView;
    protected $currentVars = [];

    public function __construct(ControllerManager $manager)
    {
        $this->version = config('view.version', 'primary');

        $p = config('view.paths', 'resource/views');

        $this->dir = "{$this->root}{$p}/";
    }

    /**
     * 分配变量到模板输出
     * 通过此方法分配的变量所有引入的模板都可用
     *
     * @param  string $key  在模板使用的变量名称
     * @param  mixed $value 变量值，此处使用引用传值，分配时变量必须先定义
     * @return static
     */
    public function share(&$key, & $value = null)
    {
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
        } else {
            $this->vars[$key] = $value;
        }

        return $this;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return $this
     */
    public function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = & $hints;
        return $this;
    }

    /**
     *
     * @return static
     */
    public function make($view, array &$vars = [])
    {
        $this->currentView = $view;
        $this->currentVars = &$vars;

        return $this;
    }

    /**
     * 读取模板内容并返回
     *
     * @param  string $viewName 模板名称
     * @param  array  $vars     要传递到模板的值，只有当前模板可以用
     * @return string
     */
    public function render($view = null, array & $vars = [])
    {
        // 页面缓存
        ob_start();

        foreach ($this->vars as $k => & $v) {
            ${$k} = & $v;
        }

        $view = $view ?: $this->currentView;
        $vars = $vars ?: $this->currentVars;

        foreach ($vars as $k => & $v) {
            ${$k} = & $v;
        }

        // 读取模板
        $path = $this->getTemplatePath($view);
        if (! is_file($path)) {
            throw new InvalidArgumentException("View [$path] not found.");
        }

        include $path;

        // 获取并清空缓存
        return ob_get_clean();
    }

    /**
     * 获取模板路径
     *
     * @param  string $name 模板名称
     * @return string
     */
    public function getTemplatePath($name)
    {
        if (isset($this->views[$name])) return $this->views[$name];

        $name = str_replace('.', '/', $name);

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = "{$this->dir}/$name.php";
    }

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasHintInformation($name)
    {
        return strpos($name, static::HINT_PATH_DELIMITER) > 0;
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        list($namespace, $view) = $this->parseNamespaceSegments($name);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param  string  $name
     * @param  array   $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as & $path) {
            $file = str_replace('.', '/', $name).'.php';

            if (is_file($viewPath = "{$this->root}{$path}/$file")) {
                return $viewPath;
            }
        }
        throw new InvalidArgumentException("View [$name] not found.");
    }

    /**
     * Get the segments of a template with a named path.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseNamespaceSegments($name)
    {
        $segments = explode(static::HINT_PATH_DELIMITER, $name);

        if (count($segments) != 2) {
            throw new InvalidArgumentException("View [$name] has an invalid name.");
        }
        if (! isset($this->hints[$segments[0]])) {
            throw new InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * 判断模板是否存在
     *
     * @param  string $name 路径名称
     * @return bool
     */
    public function exist($name)
    {
        return is_file($this->getTemplatePath($name)) ? true : false;
    }

}
