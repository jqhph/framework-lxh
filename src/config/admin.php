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

    // 是否保存操作日志
    'use-operations-log' => true,

    // 路由前缀
    'route-prefix' => 'lxh',
    // 使用默认路由
    'use-routes' => true,

    'auth' => [
        'enable' => true,
        'use-cache' => true,
        // 缓存通道
        'cache-channel' => 'admin-auth',
    ],

    // 失败几次后显示验证码
    'show-captcha-times' => 5,
    // 验证码有效时间（秒）
    'captcha-life' => 120,

    'index' => [
        // 最大tab页数量
        'max-tab' => 10,
        'default-avatar' => 'users/avatar-1.jpg',
    ],

    'operation-log' => [
        'enable'  => true,
        'handler' => 'operator',
    ],

    // 菜单模型
//    'menu-model' => '',
    // 是否使用缓存
    'menu-use-cache' => true,

    'upload' => [
        'disk' => 'admin',

        'directory' => [
            'image' => __ROOT__.'resource/uploads/images',
            'file'  => __ROOT__.'resource/uploads/files',
        ],

        'host' => '',
    ],

    'navbar-theme' => 'navbar-t1'
];