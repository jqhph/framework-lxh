<?php

return [
    'title' => 'Lxh',
    'description' => '',
    'keywords' => '',
    'author' => 'Jqh',
    // 如果是图片需要自己写img标签和宽度
    'logo' => '<span style="font-size:42px;">L<span >xh</span></span>',
    // 如果是图片需要自己写img标签和宽度
    'sm-logo' => '<b style="font-style:italic">Lxh</b>',
    'favicon' => '',
    'copyright' => '2017 @copyright JQH',

    'index' => [
        'sitebar-collapse' => true,
        'max-tab' => 10,
        'default-avatar' => 'users/avatar-1.jpg',
    ],

    'operation-log' => [
        'enable' => true,
        'handler' => 'operator',
    ],

    'upload' => [
        'disk' => 'admin',

        'directory' => [
            'image' => 'upload/image',
            'file'  => 'upload/file',
        ],

        'host' => '',
    ]
];