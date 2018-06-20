<?php
/**
 * 模板
 *
 * @author Jqh
 * @date   2017/6/15 14:53
 */

namespace Lxh\Template;

use Lxh\Exceptions\InvalidArgumentException;

class View
{
    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * 公共变量
     *
     * @var array
     */
    protected static $shares = [];

    /**
     * The array of views that have been located.
     *
     * @var array
     */
    protected static $views = [];

    /**
     * The namespace to file path hints.
     *
     * @var array
     */
    protected static $hints = [];

    /**
     * 模板名称
     *
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $root = __ROOT__;

    /**
     * 模板路径
     *
     * @var string
     */
    protected $dir;

    public function __construct($view, array $data = [])
    {
        $this->view = $view;
        $this->data = &$data;

        $p = config('view.paths', 'resource/views');
        $this->dir = "{$this->root}/{$p}/";
    }


    /**
     * 分配公共变量到模板输出
     *
     * @param  string $key  在模板使用的变量名称
     * @param  mixed $value 变量值
     */
    public static function share($key, $value = null)
    {
        if (is_array($key)) {
            static::$shares = array_merge(static::$shares, $key);
        } else {
            static::$shares[$key] = &$value;
        }
    }

    /**
     * 分配变量到模板输出
     *
     * @param  string $key  在模板使用的变量名称
     * @param  mixed $value 变量值
     * @return static
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = &$value;
        }

        return $this;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     */
    public static function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset(static::$hints[$namespace])) {
            $hints = array_merge(static::$hints[$namespace], $hints);
        }

        static::$hints[$namespace] = &$hints;
    }

    /**
     * 读取模板内容并返回
     *
     * @return string
     */
    public function render()
    {
        // 页面缓存
        ob_start();

        foreach (static::$shares as $k => &$v) {
            ${$k} = &$v;
        }

        foreach ($this->data as $k => &$v) {
            ${$k} = &$v;
        }

        // 读取模板
        $path = $this->getTemplatePath($this->view);
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
        if (isset(static::$views[$name])) return static::$views[$name];

        $name = str_replace('.', '/', $name);

        if ($this->hasHintInformation($name = trim($name))) {
            return static::$views[$name] = $this->findNamespacedView($name);
        }

        return static::$views[$name] = "{$this->dir}/$name.php";
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

        return $this->findInPaths($view, static::$hints[$namespace]);
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
        foreach ((array) $paths as &$path) {
            $file = str_replace('.', '/', $name).'.php';

            if (is_file($viewPath = "{$this->normalizePath($path)}/$file")) {
                return $viewPath;
            }
        }
        throw new InvalidArgumentException("View [$name] not found.");
    }

    protected function normalizePath(&$path)
    {
        if (strpos($path, '/') === 0 || strpos($path, ':')) {
            return $path;
        }
        return $this->root .'/'. $path;
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
        if (! isset(static::$hints[$segments[0]])) {
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
