<?php

namespace Lxh\Contracts;

use Lxh\Plugins\Plugin;

interface PluginInstaller
{
    /**
     * 安装插件时会执行此方法
     *
     * @return void
     */
    public function install(Plugin $plugin);

    /**
     * 卸载插件时会执行此方法
     *
     * @return void
     */
    public function uninstall(Plugin $plugin);
}
