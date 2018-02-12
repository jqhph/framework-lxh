<?php

namespace Lxh\Plugins\Providers;

use Lxh\Admin\Http\Controllers\Ability;
use Lxh\Admin\Http\Controllers\Role;
use Lxh\Router\Dispatcher as Router;

class Auths
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
            case 'Ability':
                $latestValue = Ability::class;
                break;
            case 'Role':
                $latestValue = Role::class;
                break;
        }

        return $latestValue;
    }
}
