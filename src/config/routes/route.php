<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 10:01
 */

return [
    [
        'pattern' => '/',
        'method' => 'GET',
        'params' => [
            'controller' => 'Index',
            'action' => 'List'
        ]
    ],

    [
        'pattern' => '/install',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'Install',
            'action' => 'install'
        ]
    ],

    [
        'pattern' => '/install/:int',
        'method' => 'GET,POST',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'Install',
            'action' => 'install',
            'step' => ':int',
        ]
    ],


    [
        'pattern' => '*',
        'method' => '*',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'namespace' => 'Lxh\Admin\Http\Controllers',
            'controller' => 'Error',
            'action' => 'NotFound'
        ]
    ]
];

