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
        'host' => '119.23.229.90',
        'port' => 3306,
        'user' => 'root',
        'pwd' => 'admin2017',
        'charset' => 'utf8',
        'name' => 'lxh',
    ],

];

return $config;
