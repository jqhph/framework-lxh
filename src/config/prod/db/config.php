<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/25
 * Time: 0:47
 */

$config = [];

$config = [
    'primary' => [
        'usepool' => false,
        'type' => 'mysql',
        'host' => '119.23.229.90',
        'port' => 3306,
        'user' => 'root',
        'pwd' => 'admin2017',
        'charset' => 'utf8',
        'name' => 'lxh',
    ],

    'local' => [
        'usepool' => false,
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'pwd' => '',
        'charset' => 'utf8',
        'name' => 'lxh',
    ],

    'she' => [
        'usepool' => false,
        'type' => 'mysql',
        'host' => '192.168.0.207',
        'port' => 3306,
        'user' => 'suitshe',
        'pwd' => 'suitshe',
        'charset' => 'utf8',
        'name' => 'suitshe',
    ],

];

return $config;
