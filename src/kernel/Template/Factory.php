<?php

namespace Lxh\Template;

use Lxh\Contracts\Container\Container;
use Lxh\Helper\Util;
use Lxh\View\View as BladeView;

class Factory
{
    const PHP   = 'php';
    const BLADE = 'blade';

    /**
     * 模板引擎类型
     *
     * @var string
     */
    protected $mode;

    /**
     * 模板版本
     *
     * @var string
     */
    protected $viewVersion;

    /**
     * @var \Lxh\View\Factory
     */
    protected $factory;

    /**
     * 当前控制器
     *
     * @var string
     */
    protected $controller;

    /**
     * 当前模块
     *
     * @var string
     */
    protected $module;

    public function __construct(Container $container)
    {
        $this->mode        = config('view.driver', static::PHP);
        $this->controller  = defined('__CONTROLLERSLUG__') ? __CONTROLLERSLUG__ : '';
        $this->module      = defined('__MODULESLUG__') ? __MODULESLUG__ : '';
        $this->viewVersion = config('view.version', 'primary');

        // 判断是否使用blade模板引擎
        if ($this->mode == static::BLADE) {
            $this->factory = $container['viewFactory'];
        }

        $this->setupNamespaces();
        fire('viewFactory.resoving', [$this]);
    }

    /**
     * 添加模板路径别名
     *
     * @return void
     */
    protected function setupNamespaces()
    {
        foreach ((array) config('view.namespaces') as $alias => &$paths) {
            $this->addNamespace($alias, alias($paths));
        }
    }

    /**
     * @param $view
     * @param array $vars
     * @param bool  $usePrefix
     * @return View|BladeView
     */
    public function make($view, array $vars = [], $usePrefix = true)
    {
        if ($this->mode == static::BLADE) {
            return $this->factory->make($this->normalizeView($view, $usePrefix), $vars);
        }

        return new View($this->normalizeView($view, $usePrefix), $vars);
    }

    public function share($k, $v = null)
    {
        if ($this->mode == static::BLADE) {
            $this->factory->share($k, $v);
        } else {
            View::share($k, $v);
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
        if ($this->mode == static::BLADE) {
            $this->factory->addNamespace($namespace, $hints);
        } else {
            View::addNamespace($namespace, $hints);
        }

        return $this;
    }

    /**
     * Normalize the given event name.
     *
     * @return string
     */
    protected function normalizeView($view, $usePrefix = true)
    {
        $prefix = '';
        if ($usePrefix && strpos($view, '::') === false) {
            if ($this->module) {
                $prefix = $this->module . '.' . $this->viewVersion;
            }
            $c = $this->controller . '.';
            if (strpos($view, $c) === false && strpos($view, $this->controller . '/') === false) {
                $view = $c . $view;
            }
        }

        return $prefix ? $prefix . '.' . $view : $view;
    }

    public function factory()
    {
        return $this->factory;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->factory, $name], $arguments);
    }

}
