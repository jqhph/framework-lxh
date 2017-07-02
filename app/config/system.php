<?php
/**
 * 系统配置
 *
 * @author Jqh
 * @date   2017/6/14 11:18
 */
$config = [];

// 配置资源服务器
$config['resource-server'] = '';

// seajs配置
$config['sea-config'] = [
    // 设置路径，方便跨目录调用
    'paths' => [
        's' => '/static/v1.0',
    ],
    // 设置别名，方便调用
    'alias' => [
        'jquery' => 's/js/jquery.min',
        'parsley' => 's/plugins/parsleyjs/dist/parsley.min',
        'container' => 's/js/container',
        'toastr' => 's/plugins/toastr/toastr.min',
        'core' => 's/js/jquery.core',
        'blade' => 's/js/blade',
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
$config['public-js'] = ['parsley', 'toastr', 'container', 'core', 'blade'];

return $config;
