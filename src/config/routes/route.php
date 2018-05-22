<?php

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
