<?php
/**
 * suitshe路由配置文件
 *
 * @author Jqh
 * @date   2017/8/15 15:34
 */
$config = [];

// 首页
$config[] = [
    'pattern' => '/',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Index',
        'action' => 'List'
    ]
];

// 新品页
$config[] = [
    'pattern' => '/404.html',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'App',
        'action' => 'NotFound'
    ]
];

// 新品页
$config[] = [
    'pattern' => '/new.html',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Produce',
        'action' => 'New'
    ]
];

// 热销页
$config[] = [
    'pattern' => '/hot.html',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Produce',
        'action' => 'Hot'
    ]
];

// 搜索页
$config[] = [
    'pattern' => '/search.html',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'App',
        'action' => 'Search'
    ]
];


// 文章界面
$config[] = [
    'pattern' => '/article/@([a-zA-Z]+)\.(html|htm)$@',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Article',
        'action' => 1
    ]
];

// 获取用户登录信息api
$config[] = [
    'pattern' => '/api/:language/auth',
    'method' => 'GET,POST',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'User',
        'action' => 'auth'
    ]
];

$config[] = [
    'pattern' => '/suitapi/:controller/:action',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => ':controller',
        'action' => ':action'
    ]
];

// 产品详情页
$config[] = [
    'pattern' => '/prod/@([a-zA-Z_]+)[-]+([\d]+)\.(html|htm)$@',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Produce',
        'action' => 'Detail',
        'id' => 2
    ]
];

// 产品详情页
$config[] = [
    'pattern' => '/cate/@([a-zA-Z_]+)[-]+([\d]+)\.(html|htm)$@',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'Sort',
        'action' => 'Detail',
        'id' => 2
    ]
];

// 伪静态页
$config[] = [
    'pattern' => '/@([a-zA-Z_]+)\.(html|htm)$@',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 1,
        'action' => 'List'
    ]
];

// 匹配任意路由，当所有路由都匹配不成功时，会匹配此路由
$config[] = [
    'pattern' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Home',
        'controller' => 'App',
        'action' => 'NotFound'
    ]
];

return $config;
