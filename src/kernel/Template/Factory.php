<?php

namespace Lxh\Template;

use Lxh\Contracts\Container\Container;
use Lxh\Helper\Util;

class Factory
{
    protected $driver = 'php';

    /**
     * @var string
     */
    protected $viewVersion;

    protected $factory;

    protected $controller;

    protected $module;

    public function __construct(Container $container)
    {
        $this->driver = config('view.driver', 'php');
        $this->controller = Util::convertWith(__CONTROLLER__, true, '-');

        $this->module = Util::convertWith(__MODULE__, true, '-');
        $this->viewVersion = config('view.version', 'primary');

        // 判断是否使用blade模板引擎
        if ($this->driver == 'blade') {
            $this->factory = $container['view.factory'];
        } else {
            $this->factory = $container['view'];
        }

        $this->setupNamespaces();
    }

    /**
     * 添加模板路径别名
     *
     * @return void
     */
    protected function setupNamespaces()
    {
        foreach ((array) config('view.namespaces') as $alias => &$paths) {
            $this->addNamespace($alias, $paths);
        }
    }

    /**
     * @param $view
     * @param array $vars
     * @return mixed
     */
    public function make($view, array &$vars = [])
    {
        return $this->factory->make($this->normalizeView($view), $vars);
    }

    public function render()
    {
        return $this->factory->render();
    }

    public function share($k, &$v = null)
    {
        return $this->factory->share($k, $v);
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
        $this->factory->addNamespace($namespace, $hints);

        return $this;
    }

    /**
     * Normalize the given event name.
     *
     * @param string $name
     * @return string
     */
    protected function normalizeView($view, $prefix = null)
    {
        if (!strpos($view, '::')) {
            if ($this->module) {
                $prefix = $this->module . '.' . $this->viewVersion;
            }
            if (strpos($view, '.') === false && strpos($view, '/') === false) {
                $view = $this->controller . '.' . $view;
            }
        }

        return $prefix ? $prefix . '.' . $view : $view;
    }

    public function get()
    {
        return $this->factory;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->factory, $name], $arguments);
    }

}
