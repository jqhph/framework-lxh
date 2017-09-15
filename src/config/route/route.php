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
    'method' => '*',
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
    'pattern' => '/api/user/login',
    'method' => 'POST',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'User',
        'action' => 'Login'
    ]
];

$config[] = [
    'pattern' => '/api/Js/:type',
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
    'pattern' => '/api/:controller/view/:id',
    'method' => 'PUT',
    'params' => [
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => 'Update',
        'id' => ':id'
    ]
];

$config[] = [
    'pattern' => '/api/:controller/view/:id',
    'method' => 'DELETE',
    'params' => [
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => 'Delete',
        'id' => ':id'
    ]
];

$config[] = [
    'pattern' => '/api/:controller',
    'method' => 'POST',
    'params' => [
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => 'Add',
        'id' => ':id'
    ]
];

$config[] = [
    'pattern' => '/api/:controller/:action',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => ':action',
    ]
];

$config[] = [
    'pattern' => '/lxh/login',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Login',
        'action' => 'Index'
    ]
];


$config[] = [
    'pattern' => 'admin/:controller/:action',
    'method' => '*',
    'params' => [
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => ':action'
    ]
];

$config[] = [
    'pattern' => 'admin/:controller/view/:id',
    'method' => 'GET',
    'params' => [
        'module' => 'Admin',
        'controller' => ':controller',
        'action' => 'Detail',
        'id' => ':id',
    ]
];

return $config;
