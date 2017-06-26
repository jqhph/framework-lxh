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
