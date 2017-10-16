<?php
/**
 * 前端路由配置文件
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/10/14
 * Time: 17:15
 */

$config = [];

$moduel = 'Client';

$config[] = [
    'pattern' => '/',
    'method' => 'GET',
    'params' => [
        'module' => & $moduel,
        'auth' => false,
        'controller' => 'Index',
        'action' => 'List'
    ]
];

$config[] = [
    'pattern' => '/api/app/wechat-auth',
    'method' => '*',
    'params' => [
        'module' => & $moduel,
        'auth' => false,
        'controller' => 'App',
        'action' => 'WechatAuth'
    ]
];

$config[] = [
    'pattern' => '/api/:lc[controller]/:lc[action]',
    'method' => '*',
    'params' => [
        'module' => & $moduel,
//        'auth' => false,
        'controller' => ':lc[controller]',
        'action' => ':lc[action]'
    ]
];

// 匹配任意路由
$config[] = [
    'pattern' => '*',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => & $moduel,
        'controller' => 'App',
        'action' => 'NotFound'
    ]
];

return $config;
