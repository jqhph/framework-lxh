<?php

namespace Lxh\Plugins;

class Dispatcher
{
    // 加载后台首页时触发
    const ADMININDEX = 'admin.index.index';
    // 加载后台dashboard页时触发
    const ADMINDASHBOARD = 'admin.index.dashboard';

    /**
     * The registered event listeners.
     *
     * @var array
     */
    protected $listeners = [];

    public function call()
    {

    }

}
