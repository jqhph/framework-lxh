<?php

namespace Lxh\Plugins\System;

use Lxh\Router\Dispatcher;

/**
 * 权限插件注册器
 *
 * @package Lxh\Plugins\Menu
 */
class Provider
{
    /**
     * @var Dispatcher
     */
    protected $router;

    public function __construct(Dispatcher $router)
    {
        $this->router = $router;
    }

    public function register()
    {
        $this->registerNotAuthRouter();

        // 由于Admin模块和admin前缀冲突，只能单独注册
        $this->registerRouter('Admin', 'admin');
        $this->registerRouter('1', '#(ability|role|menu|language)#');
    }

    protected function registerNotAuthRouter()
    {
        $prefix = 'admin';
        // 定义为后台模块
        $module = admin_name();
        $namespace = "Lxh\\Admin\\Http\\Controllers";

        $this->router->add([
            'pattern' => '/admin/api/login',
            'method' => 'POST',
            'params' => [
                'auth' => false,
                'module' => $module,
                'namespace' => &$namespace,
                'controller' => 'Admin',
                'action' => 'Login',
            ]
        ]);

        $this->router->add([
            'pattern' => '/admin/api/js/:lc@type',
            'method' => 'GET',
            'params' => [
                'auth' => false,
                'module' => $module,
                'namespace' => &$namespace,
                'controller' => 'Js',
                'action' => 'Entrance',
                'type' => ':lc@type',
            ]
        ]);
    }

    /**
     * 注册路由
     *
     * @param $controller
     * @param $dash
     */
    protected function registerRouter($controller, $dash)
    {
        $prefix = 'admin';
        // 定义为后台模块
        $module = admin_name();
        $namespace = "Lxh\\Admin\\Http\\Controllers";

        // 常用路由规则注册示例
        // 如不需要请删除
        $this->router->attach([
            // 菜单列表页 /admin/$dash/action/list
            //    新建页 /admin/$dash/action/create
            [
                'pattern' => "/$prefix/$dash/action/:lc@a",
                'method' => 'GET',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => ':lc@a',
                ]
            ],

            // 详情页 /admin/$dash/view/1
            [
                'pattern' => "/$prefix/$dash/view/:int@id",
                'method' => 'GET',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => 'Detail',
                    'id' => ':int@id',
                ]
            ],

            // 菜单新增接口
            [
                'pattern' => "/$prefix/api/$dash",
                'method' => 'POST',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => 'Add',
                ]
            ],

            // 修改接口
            [
                'pattern' => "/$prefix/api/$dash/view/:int@id",
                'method' => 'PUT',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => 'Update',
                    'id' => ':int@id'
                ]
            ],

            // 删除接口
            [
                'pattern' => "/$prefix/api/$dash/:int@id",
                'method' => 'DELETE',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => 'Delete',
                    'id' => ':int@id'
                ]
            ],

            // 批量删除接口
            [
                'pattern' => "/$prefix/api/$dash/batch-delete",
                'method' => 'POST,DELETE',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => 'BatchDelete'
                ]
            ],

            // api带id参数接口
            [
                'pattern' => "/$prefix/api/$dash/:lc@a/:int@id",
                'method' => 'GET,POST',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => ':lc@a',
                    'id' => ':int@id'
                ]
            ],

            // api带id参数接口
            [
                'pattern' => "/$prefix/api/$dash/:lc@a",
                'method' => 'GET,POST',
                'params' => [
                    'module' => $module,
                    'namespace' => &$namespace,
                    'controller' => $controller,
                    'action' => ':lc@a'
                ]
            ],
        ]);

    }

}
