<?php

namespace GetsWhatsApp;

use Lxh\Container\Container;
use Lxh\Contracts\PluginRegister;
use Lxh\Router\Dispatcher;

class Application implements PluginRegister
{
    /**
     * @var Dispatcher
     */
    protected $router;

    public function __construct()
    {
        $this->router = resolve('router');
    }

    /**
     * 插件注册方法
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
    }

    /**
     * 注册路由
     * 为防止冲突，请尽量使用长一点的url
     *
     * @return void
     */
    protected function registerRouter()
    {
        $prefix = 'whatsapp';
        $module = 'Admin';
        $namespace = "GetsWhatsApp\\Http\\Controllers";

        $this->router->attach([
            [
                'pattern' => "/$prefix/test",
                'method' => 'GET',
                'params' => [
                    'auth' => false,
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => 'Message',
                    'action' => 'Test'
                ]
            ],
        ]);
    }
}
