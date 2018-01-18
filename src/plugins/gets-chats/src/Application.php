<?php

namespace GetsChats;

use Lxh\Auth\Menu;
use Lxh\Container\Container;
use Lxh\Contracts\PluginRegister;
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
        // 注册路由示例，如不需要请删除
        $this->registerRouter();

        // 创建菜单示例，如不需要请删除
        $this->registerMenu();

    }

    protected function registerMenu()
    {
        listen(EVENT_MENU_RESOLVING, function (Menu $menu) {
            // 权限判断
            if (auth()->can('gets-chats')) {
                $menu->addPlugin($menu->buildRow('test', '/gets-chats/test'));
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
        $prefix = 'gets-chats';
        // 定义为后台模块
        $module = admin_name();
        $namespace = "GetsChats\\Http\\Controllers";

        $this->router->attach([
            [
                'pattern' => "/$prefix/test",
                'method' => 'GET',
                'params' => [
                    'auth' => false,
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => 'Test',
                    'action' => 'Test'
                ]
            ],
        ]);
    }
}
