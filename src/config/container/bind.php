<?php
/**
 * 服务注册文件
 *
 * @author Jqh
 * @date   2017/6/13 20:15
 */

use Lxh\Config\Config;
use Lxh\Contracts\Container\Container;
use Lxh\View\ViewServiceProvider;
use EasyWeChat\Foundation\Application as Wechat;

$container = container();

// 注册easywechat application
$container->singleton('wechat', function () {
    $opts = config('easy-wechat');

    return new Wechat($opts);
});
