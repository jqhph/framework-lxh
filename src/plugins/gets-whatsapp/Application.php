<?php

namespace GetsWhatsApp;

use Lxh\Container\Container;
use Lxh\Contracts\PluginRegister;

class Application implements PluginRegister
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
    }

    /**
     * 插件注册方法
     *
     * @return void
     */
    public function register()
    {
    }
}
