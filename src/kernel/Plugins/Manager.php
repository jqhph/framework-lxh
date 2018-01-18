<?php

namespace Lxh\Plugins;

class Manager
{
    /**
     * 所有插件
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * @var Register
     */
    protected $register;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Filter
     */
    protected $filter;

    public function __construct()
    {
        $this->register = new Register($this);
    }

    /**
     * @return Register
     */
    public function register()
    {
        return $this->register;
    }

    /**
     * @return Installer
     */
    public function installer($plugin)
    {
        return new Installer($this, $plugin);
    }

    /**
     * 插件调度器
     *
     * @return Dispatcher
     */
    public function dispatcher()
    {
        return $this->dispatcher ?: ($this->dispatcher = new Dispatcher($this));
    }

    /**
     * 页面过滤器
     *
     * @return Filter
     */
    public function filter()
    {
        return $this->filter ?: ($this->filter = new Filter($this));
    }

    /**
     * @param $name
     * @return Plugin
     */
    public function plugin($name)
    {
        return new Plugin($this, $name);
    }

    /**
     * 获取所有插件名称
     *
     * @return array
     */
    public function plugins()
    {
//        return $this->plugins ?: ($this->plugins = files()->getFileList());
    }

    /**
     * 获取插件路径
     *
     * @return string
     */
    public function getPluginPath($plugin)
    {
        return __PLUGINS__ . $plugin;
    }

    /**
     * 如果插件存在则返回插件路径
     *
     * @param $plugin
     * @return string|bool
     */
    public function getPluginPathIfExist($plugin)
    {
        return is_file($path = $this->getPluginPath($plugin)) ? $path : false;
    }
    
}
