<?php

return [
    [
        'pattern' => '/',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'controller' => 'Install',
            'action' => 'install'
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

