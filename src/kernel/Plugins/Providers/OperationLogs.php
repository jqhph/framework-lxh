<?php

namespace Lxh\Plugins\Providers;

use Lxh\Router\Dispatcher as Router;

class OperationLogs
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
    }

    /**
     *
     * @return string
     */
    public function setController($controller, $latestValue)
    {
        switch ($latestValue) {
            case 'Logs':
                $latestValue = \Lxh\Admin\Http\Controllers\Logs::class;
                break;
        }

        return $latestValue;
    }
}

