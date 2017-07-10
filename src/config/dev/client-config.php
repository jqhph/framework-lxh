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
$base = "{$config['resource-server']}/static/{$config['resource-version']}";
$config['sea-config'] = [
    // 设置路径，方便跨目录调用
    'paths' => [
        's' => $base,
        'lib' => "$base/lib",
        'api' => '/api/Js',
        'view' => "$base/view",
        'module' => "$base/view/module",
    ],
    // 设置别名，方便调用
    'alias' => [
        'jquery' => 'lib/js/jquery.min',
        'parsley' => 'lib/plugins/parsleyjs/dist/parsley.min',
        'container' => 'lib/js/container',
        'toastr' => 'lib/plugins/toastr/toastr.min',
        'core' => 'lib/js/jquery.core',
        'blade' => 'lib/js/blade',
        'validate' => 'lib/js/validate',
        'router' => 'lib/js/router',
    ]
];

// 引入默认css
$config['public-css'] = [
    'lib/plugins/toastr/toastr.min.css',
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
//    'router',
];

$config['route-init'] = [
    'mode' => 'hash',
    'root' => '/'
];

$config['routes'] = [
    '/',
    '/:controller',
    '/:controller/action/:action',
    '/:controller/view/:id',

    // '/:controller/' => '',
];

return $config;
