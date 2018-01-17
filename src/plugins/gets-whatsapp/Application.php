<?php

namespace GetsWhatsApp;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Menu;
use Lxh\Container\Container;
use Lxh\Contracts\PluginRegister;
use Lxh\Plugins\Dispatcher;
use Lxh\Router\Dispatcher as Router;

class Application implements PluginRegister
{
    /**
     * @var Router
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

        // 创建菜单
        listen(EVENT_MENU_RESOLVING, function (Menu $menu) {
            // 权限判断
            if (auth()->can('whatsapp')) {
                $menu->addPlugin($menu->buildRow('test', '/whatsapp/test'));
            }
        });

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
        // 后台模块
        $module = admin_name();
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
