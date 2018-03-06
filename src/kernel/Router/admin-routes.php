<?php
/**
 * Admin模块默认路由
 *
 * @author Jqh
 * @date   2018/1/17 09:44
 */

$module = 'Admin';

return [
    // 后台首页路由（最顶级iframe）
    [
        'pattern' => '/admin',
        'method' => 'GET',
        'params' => [
            'module' => 'Admin',
            'controller' => 'Index',
            'action' => 'Index'
        ]
    ],

    // 登录页面
    [
        'pattern' => '/admin/login',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'Login',
            'action' => 'Index'
        ]
    ],

    // 登录接口
    [
        'pattern' => '/admin/api/login',
        'method' => 'POST',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => 'Admin',
            'controller' => 'Admin',
            'action' => 'Login'
        ]
    ],

    // api带id参数接口
    [
        'pattern' => '/admin/api/:lc@c/:lc@a/:int@id',
        'method' => 'GET',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => ':lc@a',
            'id' => ':int@id'
        ]
    ],

    // js加载接口
    [
        'pattern' => '/admin/api/js/:type',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => 'Admin',
            'controller' => 'Js',
            'action' => 'Entrance',
            'type' => ':type'
        ]
    ],

    // 图片阅读
    [
        'pattern' => '/image/:word@dir/:filename',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => 'Admin',
            'controller' => 'Image',
            'action' => 'read',
            'filename' => ':filename',
            'dir' => ':word@dir',
        ]
    ],

    // 修改接口
    [
        'pattern' => '/admin/api/:lc@c/view/:int@id',
        'method' => 'POST',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Update',
            'id' => ':int@id'
        ]
    ],
    [
        'pattern' => '/admin/api/:lc@c/update-field/:int@id',
        'method' => 'POST',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'UpdateField',
            'id' => ':int@id'
        ]
    ],

    // 删除接口
    [
        'pattern' => '/admin/api/:lc@c/:int@id',
        'method' => 'DELETE',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Delete',
            'id' => ':int@id'
        ]
    ],

    // 批量删除接口
    [
        'pattern' => '/admin/api/:lc@c/batch-delete',
        'method' => 'POST,DELETE',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'BatchDelete'
        ]
    ],

    // 新增接口
    [
        'pattern' => '/admin/api/:lc@c',
        'method' => 'POST',
        'params' => [
            'module' => 'Admin',
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Add'
        ]
    ],

    // 自定义访问API控制器和action接口
    [
        'pattern' => '/admin/api/:lc@c/:lc@a',
        'method' => '*',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => 'Admin',
            'controller' => ':lc@c',
            'action' => ':lc@a',
        ]
    ],

    // 普通页面action
    [
        'pattern' => 'admin/:lc@c/action/:lc@a',
        'method' => '*',
        'params' => [
            'module' => 'Admin',
            'controller' => ':lc@c',
            'action' => ':lc@a'
        ]
    ],

    // 详情页
    [
        'pattern' => 'admin/:lc@c/view/:int',
        'method' => 'GET',
        'params' => [
            'module' => 'Admin',
            'controller' => ':lc@c',
            'action' => 'Detail',
            'id' => ':int',
        ]
    ]
];
