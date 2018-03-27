<?php
/**
 * Admin模块默认路由
 *
 * @author Jqh
 * @date   2018/1/17 09:44
 */

$module = 'Admin';

$prefix = '/'.config('admin.route-prefix');

return [
    // 后台首页路由（最顶级iframe）
    [
        'pattern' => &$prefix,
        'method' => 'GET',
        'params' => [
            'module' => $module,
            'controller' => 'Index',
            'action' => 'Index'
        ]
    ],

    // 登录页面
    [
        'pattern' => $prefix.'/login',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => $module,
            'controller' => 'Login',
            'action' => 'Index'
        ]
    ],

    // 登录页面
    [
        'pattern' => $prefix.'/captcha',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => $module,
            'controller' => 'Login',
            'action' => 'Captcha'
        ]
    ],

    // 登录页面
    [
        'pattern' => $prefix.'/logout',
        'method' => 'GET',
        'params' => [
            'module' => 'Admin',
            'controller' => 'Admin',
            'action' => 'Logout'
        ]
    ],

    [
        'pattern' => $prefix.'/api/js/:lc@type',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => $module,
            'namespace' => 'Lxh\\Admin\\Http\\Controllers',
            'controller' => 'Js',
            'action' => 'Entrance',
            'type' => ':lc@type',
        ]
    ],

    // 登录接口
    [
        'pattern' => $prefix.'/api/login',
        'method' => 'POST',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => $module,
            'controller' => 'Admin',
            'action' => 'Login'
        ]
    ],

    // api带id参数接口
    [
        'pattern' => $prefix.'/api/:lc@c/:lc@a/:int@id',
        'method' => 'GET',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => ':lc@a',
            'id' => ':int@id'
        ]
    ],

    // js加载接口
    [
        'pattern' => $prefix.'/api/js/:type',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => $module,
            'controller' => 'Js',
            'action' => 'Entrance',
            'type' => ':type'
        ]
    ],

    // 数据还原接口
    [
        'pattern' => $prefix.'/api/:lc@c/restore',
        'method' => 'POST',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Restore'
        ]
    ],

    // 图片阅读
    [
        'pattern' => '/image/:word@dir/:filename',
        'method' => 'GET',
        'params' => [
            'auth' => false,
            'module' => $module,
            'controller' => 'Image',
            'action' => 'read',
            'filename' => ':filename',
            'dir' => ':word@dir',
        ]
    ],

    // 修改接口
    [
        'pattern' => $prefix.'/api/:lc@c/view/:int@id',
        'method' => 'POST',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Update',
            'id' => ':int@id'
        ]
    ],
    [
        'pattern' => $prefix.'/api/:lc@c/update-field/:int@id',
        'method' => 'POST',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'UpdateField',
            'id' => ':int@id'
        ]
    ],

    // 删除接口
    [
        'pattern' => $prefix.'/api/:lc@c/:int@id',
        'method' => 'DELETE',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Delete',
            'id' => ':int@id'
        ]
    ],

    // 批量删除接口
    [
        'pattern' => $prefix.'/api/:lc@c/batch-delete',
        'method' => 'POST,DELETE',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'BatchDelete'
        ]
    ],

    // 新增接口
    [
        'pattern' => $prefix.'/api/:lc@c',
        'method' => 'POST',
        'params' => [
            'module' => $module,
            'api' => true,
            'controller' => ':lc@c',
            'action' => 'Add'
        ]
    ],

    // 自定义访问API控制器和action接口
    [
        'pattern' => $prefix.'/api/:lc@c/:lc@a',
        'method' => '*',
        'params' => [
            'auth' => false,
            'api' => true,
            'module' => $module,
            'controller' => ':lc@c',
            'action' => ':lc@a',
        ]
    ],

    // 普通页面action
    [
        'pattern' => $prefix.'/:lc@c/action/:lc@a',
        'method' => '*',
        'params' => [
            'module' => $module,
            'controller' => ':lc@c',
            'action' => ':lc@a'
        ]
    ],

    // 详情页
    [
        'pattern' => $prefix.'/:lc@c/view/:int',
        'method' => 'GET',
        'params' => [
            'module' => $module,
            'controller' => ':lc@c',
            'action' => 'Detail',
            'id' => ':int',
        ]
    ]
];
