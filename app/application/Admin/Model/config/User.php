<?php
/**
 * 用户模型配置文件
 *
 * @author Jqh
 * @date   2017/6/28 22:07
 */

$config = [];

$config['fields'] = [
    'id' => [
        'type' => 'int',
        'disabled' => true,
        'attrs' => [

        ],
    ],
    'username' => [
        'type' => 'char',
        'attrs' => [
            'lengthBetween' => [4, 20],

        ],
    ],
    'password' => [
        'type' => 'password',
        'attrs' => [
            'lengthBetween' => [4, 20],
        ],
    ],
    'email' => [
        'type' => 'email',
        'attrs' => [

        ],
    ],
    'mobile' => [
        'type' => 'phone',
        'attrs' => [

        ],
    ],
    'first_name' => [
        'type' => 'char',
        'attrs' => [

        ],
    ],
    'last_name' => [
        'type' => 'char',
        'attrs' => [

        ],
    ],
    'avatar' => [
        'type' => 'avatar',
        'attrs' => [

        ],
    ],
    'sex' => [
        'type' => 'enum',
        'options' => [
            0, 1, 2
        ],
        'attrs' => [

        ],
    ],
    'createdAt' => [
        'type' => 'date',
        'disabled' => true,
        'attrs' => [

        ]
    ],
    'lastLoginIp' => [
        'type' => 'char',
        'disabled' => true,
        'attrs' => [

        ]
    ],

];

$config['map'] = [
    'createdAt' => 'created_at',
    'lastLoginIp' => 'last_login_ip',
    'lastLoginTime' => 'last_login_time',
    'regIp' => 'reg_ip'
];

return $config;
