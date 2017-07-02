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
//        'module' => 'Home',
        'controller' => 'Index',
        'action' => 'Index'
    ]
];

$config[] = [
    'pattern' => '/Register',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Login',
        'action' => 'Register'
    ]
];

$config[] = [
    'pattern' => '/api/User/Login',
    'method' => 'POST',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'User',
        'action' => 'Login'
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
    'pattern' => '/Lxh/Login',
    'method' => 'GET',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'Login',
        'action' => 'Index'
    ]
];


$config[] = [
    'pattern' => '/:controller/:action',
    'method' => '*',
    'params' => [
        'controller' => ':controller',
        'action' => ':action'
    ]
];

return $config;
