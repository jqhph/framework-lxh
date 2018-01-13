<?php
/**
 * 前端配置文件.
 * User: Jqh
 * Date: 2017/7/2
 * Time: 20:59
 */

$config = [];

// 是否启用缓存 ===> 已移至可写配置文件
//$config['use-cache'] = true;
// 前端缓存的默认缓存时间，单位毫秒 ===> 已移至可写配置文件
//$config['cache-expire'] = 259200 * 1000;
// 语言包缓存时间，单位毫秒 ===> 已移植可写配置文件
//$config['lang-package-expire'] = 259200 * 1000; // 缓存时间，3天

// 配置资源服务器
$config['resource-server'] = '';

// resource version静态资源版本
$config['resource-version'] = 'primary';

// seajs配置
$base = "{$config['resource-server']}/assets/{$config['resource-version']}/Admin";
$config['sea-config'] = [
    // 设置路径，方便跨目录调用
    'paths' => [
        's' => $base,
        'lib' => "$base/lib",
        'api' => '/api/js',
        'view' => "$base/view",
        'module' => "$base/view/module",
        'css' => "$base/css",
        'admin' => "$base/components",
    ],
    // 设置别名，方便调用
    'alias' => [
        'parsley' => 'lib/plugins/parsleyjs/dist/parsley.min',
        'container' => 'lib/js/container.min',
        'toastr' => 'lib/plugins/toastr/toastr.min',
        'core' => 'lib/js/jquery.core.min',
        'validate' => 'lib/js/validate',
    ]
];

// 引入默认css
$config['public-css'] = [
    's/css/responsive.min.css'
];

// 引入默认js
$config['public-js'] = [
    'container',
];

return $config;
