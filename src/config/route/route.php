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
    'pattern' => '/api/:lowercase[controller]/view/:numbers[id]',
    'method' => 'PUT',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => 'Update',
        'id' => ':numbers[id]'
    ]
];

$config[] = [
    'pattern' => '/api/:lowercase[controller]/view/:numbers[id]',
    'method' => 'DELETE',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => 'Delete',
        'id' => ':numbers[id]'
    ]
];

$config[] = [
    'pattern' => '/api/:lowercase[controller]/:numbers[id]',
    'method' => 'POST',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => 'Add',
        'id' => ':numbers[id]'
    ]
];

$config[] = [
    'pattern' => '/api/:lowercase[controller]/:lowercase[action]',
    'method' => '*',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => ':lowercase[action]',
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
    'pattern' => 'admin/:lowercase[controller]/:lowercase[action]',
    'method' => '*',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => ':lowercase[action]'
    ]
];

$config[] = [
    'pattern' => 'admin/:lowercase[controller]/view/:numbers',
    'method' => 'GET',
    'params' => [
        'module' => 'Admin',
        'controller' => ':lowercase[controller]',
        'action' => 'Detail',
        'id' => ':numbers',
    ]
];

return $config;
