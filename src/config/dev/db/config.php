<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/25
 * Time: 0:47
 */

$config = [];

$config['db'] = [
    'primary' => [
        'usepool' => false,
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'pwd' => '',
        'charset' => 'utf8',
        'name' => 'lxh',
    ],

];

return $config;