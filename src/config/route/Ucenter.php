<?php
/**
 * 用户中心路由配置
 *
 * @author Jqh
 * @date   2017/8/18 15:12
 */

$module = 'Ucenter';

$config[] = [
    'pattern' => '/',
    'method' => '*',
    'params' => [
        'module' => & $module,
        'controller' => 'Index',
        'action' => 'List'
    ]
];

// 用户登录url
$config[] = [
    'pattern' => '/login',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => & $module,
        'controller' => 'User',
        'action' => 'LoginPage'
    ]
];

// 登陆验证
$config[] = [
    'pattern' => '/auth',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => & $module,
        'controller' => 'User',
        'action' => 'Auth'
    ]
];

// api路由
$config[] = [
    'pattern' => '/api/:controller/:action',
    'method' => '*',
    'params' => [
        'auth' => 'Api',
        'module' => & $module,
        'controller' => ':controller',
        'action' => ':action'
    ]
];

// 普通路由
$config[] = [
    'pattern' => '/:controller/action/:action',
    'method' => '*',
    'params' => [
        'module' => & $module,
        'controller' => ':controller',
        'action' => ':action'
    ]
];

// 匹配任意路由，当所有路由都匹配不成功时，会匹配此路由
$config[] = [
    'pattern' => '*',
    'params' => [
        'auth' => false,
        'module' => & $module,
        'controller' => 'App',
        'action' => 'NotFound'
    ]
];

return $config;
