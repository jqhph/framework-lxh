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
        'pattern' => '/dev',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'Test',
            'action' => 'Test'
        ]
    ],

    [
        'pattern' => '*',
        'method' => '*',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'App',
            'action' => 'NotFound'
        ]
    ]
];

