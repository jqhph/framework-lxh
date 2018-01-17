<?php

namespace Lxh\Plugins;

class Register
{
    /**
     * 需要注册的插件
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * 已注册插件
     *
     * @var array
     */
    protected $registered = [];

    /**
     * 注册失败的插件
     *
     * @var array
     */
    protected $unregistered = [];

    public function __construct()
    {
        $this->plugins = (array) config('plugins');
    }

    /**
     * 开始注册插件
     *
     * @return void
     */
    public function handle()
    {

    }
}
