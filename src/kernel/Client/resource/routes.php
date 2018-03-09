<?php

// 前台路由配置
return [
    // 后台首页路由（最顶级iframe）
    [
        'pattern' => '/',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => 'Home',
            'controller' => 'Index',
            'action' => 'Index'
        ]
    ],
];
