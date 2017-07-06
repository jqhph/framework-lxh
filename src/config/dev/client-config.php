<?php
/**
 * 前端配置文件.
 * User: Jqh
 * Date: 2017/7/2
 * Time: 20:59
 */

$config = [];

// 是否启用缓存
$config['use-cache'] = true;

// 前端缓存的默认缓存时间，单位毫秒
$config['cache-expire'] = 259200 * 1000;

// 语言包缓存时间，单位毫秒
$config['lang-package-expire'] = 259200 * 1000; // 缓存时间，3天

// 配置资源服务器
$config['resource-server'] = '';

// resource version静态资源版本
$config['resource-version'] = 'v1.0';

// seajs配置
$config['sea-config'] = [
    // 设置路径，方便跨目录调用
    'paths' => [
        's' => "{$config['resource-server']}/static/{$config['resource-version']}",
        'api' => '/api/Js'
    ],
    // 设置别名，方便调用
    'alias' => [
        'jquery' => 's/js/jquery.min',
        'parsley' => 's/plugins/parsleyjs/dist/parsley.min',
        'container' => 's/js/container',
        'toastr' => 's/plugins/toastr/toastr.min',
        'core' => 's/js/jquery.core',
        'blade' => 's/js/blade',
        'validate' => 's/js/validate',
        'router' => 's/js/router',
    ]
];

// 引入默认css
$config['public-css'] = [
    's/plugins/toastr/toastr.min.css',
    's/css/core.css',
    's/css/components.css',
    's/css/icons.css',
    's/css/pages.css',
];

// 引入默认js
$config['public-js'] = [
    'validate',
    'toastr',
    'container',
    'core',
    'blade',
    'router',
];

$config['route-init'] = [
    'mode' => 'hash',
    'root' => '/'
];

$config['route'] = [
    
];

return $config;
