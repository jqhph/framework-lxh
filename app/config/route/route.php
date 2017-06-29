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
    'pattern' => '/api/User/action/Login',
    'method' => 'POST',
    'params' => [
        'auth' => false,
        'module' => 'Admin',
        'controller' => 'User',
        'action' => 'Login'
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
