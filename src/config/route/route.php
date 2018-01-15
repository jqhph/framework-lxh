<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 10:01
 */

$config = [];

$config[] = [
    'pattern' => '/',
    'method' => 'GET',
    'params' => [
        'controller' => 'Index',
        'action' => 'List'
    ]
];

$config[] = [
    'pattern' => '/dev',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Test',
        'action' => 'Test'
    ]
];

$config[] = [
    'pattern' => '/admin',
    'method' => 'GET',
    'params' => [
        'module' => 'Admin',
        'controller' => 'Index',
        'action' => 'List'
    ]
];

$config[] = [
    'pattern' => '/register',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Login',
        'action' => 'Register'
    ]
];

$config[] = [
    'pattern' => '/api/admin/login',
    'method' => 'POST',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Admin',
        'action' => 'Login'
    ]
];

// 根据管理员id获取角色列表
//$config[] = [
//    'pattern' => '/api/admin/roles-list/:int[id]',
//    'method' => 'GET',
//    'params' => [
//        'module' => 'Admin',
//        'controller' => 'Admin',
//        'action' => 'RoleList',
//        'id' => ':int[id]'
//    ]
//];

$config[] = [
    'pattern' => '/api/:lc@c/:lc@a/:int@id',
    'method' => 'GET',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => ':lc@a',
        'id' => ':int@id'
    ]
];

$config[] = [
    'pattern' => '/api/js/:type',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Js',
        'action' => 'Entrance',
        'type' => ':type'
    ]
];

$config[] = [
    'pattern' => '/api/:lc@c/view/:int@id',
    'method' => 'PUT',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => 'Update',
        'id' => ':int@id'
    ]
];

// 删除单行数据路由
$config[] = [
    'pattern' => '/api/:lc@c/:int@id',
    'method' => 'DELETE',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => 'Delete',
        'id' => ':int@id'
    ]
];

// 批量删除路由
$config[] = [
    'pattern' => '/api/:lc@c/batch-delete',
    'method' => 'POST,DELETE',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => 'BatchDelete'
    ]
];

$config[] = [
    'pattern' => '/api/:lc@c',
    'method' => 'POST',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => 'Add',
        'id' => ':int[id]'
    ]
];

$config[] = [
    'pattern' => '/api/:lc@c/:lc@a',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => ':lc@a',
    ]
];

$config[] = [
    'pattern' => '/admin/login',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Login',
        'action' => 'Index'
    ]
];


$config[] = [
    'pattern' => 'admin/:lc@c/:lc@a',
    'method' => '*',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => ':lc@a'
    ]
];

$config[] = [
    'pattern' => 'admin/:lc@c/view/:int',
    'method' => 'GET',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lc@c',
        'action' => 'Detail',
        'id' => ':int',
    ]
];

// 匹配任意路由
$config[] = [
    'pattern' => '*',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'App',
        'action' => 'NotFound'
    ]
];

return $config;
