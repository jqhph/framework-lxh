<?php

return [
    'title' => 'Lxh',
    'description' => '',
    'keywords' => '',
    'author' => 'Jqh',
    // 如果是图片需要自己写img标签和宽度
    'logo' => '<span style="font-size:42px;">L<span >xh</span></span>',
    // 如果是图片需要自己写img标签和宽度
    'sm-logo' => '<font style="font-style:italic">Lxh</font>',
    'favicon' => '',
    'copyright' => '2017 @copyright JQH',

    // 使用默认路由
    'use-admin-routes' => true,

    // 登录session保存的key
    'session-key' => '$admin',

    'index' => [
        'sitebar-collapse' => true,
        'max-tab' => 10,
        'default-avatar' => 'users/avatar-1.jpg',
    ],

    'operation-log' => [
        'enable' => true,
        'handler' => 'operator',
    ],

    // 菜单模型
    'menu-model' => '',
    // 是否使用缓存
    'menu-use-cache' => true,

    'upload' => [
        'disk' => 'admin',

        'directory' => [
            'image' => 'upload/image',
            'file'  => 'upload/file',
        ],

        'host' => '',
    ]
];