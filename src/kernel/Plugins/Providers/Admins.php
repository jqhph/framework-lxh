<?php

namespace Lxh\Plugins\Providers;

use Lxh\Router\Dispatcher as Router;

class Admins
{
    /**
     * @var Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * 服务注册方法
     *
     * @return void
     */
    public function register()
    {
        add_filter('setting.controller', [$this, 'setController']);

        // 注册免验证路由
        $this->registerNotAuthRouter();
    }

    /**
     *
     * @return string
     */
    public function setController($controller, $latestValue)
    {
        switch ($latestValue) {
            case 'Admin':
                $latestValue = \Lxh\Admin\Http\Controllers\Admin::class;
                break;
        }

        return $latestValue;
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

}
