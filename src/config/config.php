<?php
/**
 * 公共配置文件
 *
 * @author admin
 * @date   2017/6/13 18:18
 */
$config = [
    // 时区配置
    'timezone' => 'PRC',

    // 是否开启语言包功能
    'use-language' => true,
    
    // 配置composer.phar安装路径
    // 如果composer是全局安装，则无需配置此参数
    'composer.working-path' => '',

    // 使用RBAC权限管理
    'use-authorize' => true,

    // 缓存配置
    'cache' => [
        // 默认缓存通道
        'primary' => [
            'use' => true,
            'driver' => 'file',
            // 缓存目录，此参数默认与通道名称相同
            'type' => 'primary',
            // 缓存前缀
            'prefix' => '',
            // 缓存根目录，默认 data/file-cache/
            'path' => __DATA_ROOT__ . 'file-cache/',
        ],
        // 后台菜单缓存通道
        'admin-menu' => [
            'driver' => 'file',
            'prefix' => '_m_',
        ],

        // 后台权限管理通道
        'admin-auth' => [
            'driver' => 'file',
            'prefix' => '_ada_',
        ],

        // 后台登录组件缓存通道
        'admin-request-auth' => [
            'prefix' => '_reqadmin_',
        ],
    ],

    // request-auth配置
    'request-auth' => [
        // 后台用户
        'admin' => [
            // 用户模型
            'model' => 'Admin',
            // 是否不限制用户使用多个客户端登录
            'allowed-multiple-logins' => false,
            // 记住登录状态时间，默认7天
            'remember-life'           => 604800,
            // 是否存储登录日志
            'storable'                => true,
            // 登陆日志模型名称
            'log-model'               => 'admin_login_log',
            // 处理登录功能驱动
            'driver'                  => Lxh\RequestAuth\Drivers\Session::class,
            // 缓存通道，默认request-auth
            'cache-channel'           => 'admin-request-auth',
            // password_hash, sha256
            'encrypt'                 => 'sha256',
            // 应用类型，必须是一个0-99的整数
            // 对于站内session模式登录，此参数用于保证用户在同一类型下的应用只能保留一个有效的登陆状态
            // 对于开放授权token模式登录，此参数用于保证用户在同一类型下的应用只能获取一个有效授权token
            'app'                     => 0,
            // 连续登陆错误长间隔时间（秒）
            'reject-interval'         => 600,
            // 用于区分前后台用户
            'user-type'               => 1
        ],

    ],

    'view' => [
        // 模板文件路径
        'paths' => 'resource/views',
        // 模板版本
        'version' => 'primary',
        // 模板引擎类型，支持 php、blade
        'driver' => 'php',
        // 视图模板路径别名
        'namespaces' => [
            'admin' => 'kernel/Admin/views',
        ],
        // blade模板缓存路径，默认'resource/blade-cache'
        'compiled' => 'resource/blade-cache',
    ],

    // 系统插件使用
    'providers' => [
        // 使用菜单功能插件
        Lxh\Plugins\Providers\Menu::class,
        // 权限系统管理功能
        Lxh\Plugins\Providers\Auths::class,
        // 默认管理员功能
        Lxh\Plugins\Providers\Admins::class,
        // 系统操作日志功能
        Lxh\Plugins\Providers\OperationLogs::class,
    ],

    // 是否输出控制台调试信息，默认true
    'response-console-log' => true,
    // 是否输出追踪信息到控制台，默认true
    'response-trace-log' => true,

    // session配置
    'session' => [
        'auto-start' => true, // 实例化Session类时开启session_start
//        'use-trans-sid' => '',
//        'name' => '', // session id
//        'path' => '/', // session path
//        'domain' => '',
//        'expire' => '1440', // 有效期
//        'secure' => '',
//        'httponly' => true,
//        'use-cookies' => true,
//        'cache-limiter' => '',
//        'cache-expire' => '',
//        'driver' => '', // 自定义session处理器
    ],

    'cookie' => [
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // 模块设定, 支持多模块, 多个用数组表示
    // 如果设置了多模块, 且没有开启子域名部署, 配置路由的时候默认指向第一个模块, 如需走第二个模块需要在路由配置中使用"module"参数指定
    'modules' => ['Admin', 'Client'],

    // 开启子域名部署，根据域名加载不同的路由配置文件
    'domain-deploy' => true,
    'domain-deploy-config' => [
        // dev.lxh.com 使用 admin.php 路由配置文件
        'dev.lxh.com' => 'admin',
        'www.lxh.com' => 'client',
    ],

    'record-error-info-level' => [
        E_ERROR,
        E_RECOVERABLE_ERROR,
        E_WARNING,
        E_PARSE,
        E_NOTICE,
        E_STRICT,
        E_DEPRECATED,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_USER_DEPRECATED
    ],

    /**
     * 事件监听配置
     *
     * route.dispatch.before 路由匹配前触发
     * route.dispatch.after  路由匹配成功后触发
     * auth.success          用户登录验证成功后触发
     * response.send.before  输出内容给浏览器之前触发
     * exception             抛出异常时触发（异常处理事件）
     */
    'events' => [
        EVENT_RESPONSE_BEFORE => [
        ],
        EVENT_RESPONSE_AFTER => [
        ],
        // 异常报告
        EVENT_EXCEPTION_REPORT => [],
        EVENT_ROUTE_DISPATCH_BEFORE => [],
        EVENT_ROUTE_DISPATCH_AFTER => [],

        'db.connect' => 'Lxh\Database\Events\Database@connect',
        'db.query' => 'Lxh\Database\Events\Database@query',
        'db.exception' => 'Lxh\Database\Events\Database@exception',
    ],

    // 公共中间件配置（路由匹配成功后，用户登录验证之前）
    'middlewares' => [
        '*' => [

        ],
        'Admin' => [
//        Lxh\Admin\Middleware\Globals::class,
        ],
    ]
];

// 日志配置
$config['logger'] = [
    // 默认日志通道
    'primary' => [
        'path'     => '../data/logs/record.log',
        'handlers' => [
            [
                'handler' 	=> Lxh\Logger\Handler\DaysFileHandler::class,
                'formatter' => Lxh\Logger\Formatter\TextFormatter::class,
                'level' 	=> '100'
            ]
        ],
        'maxFiles' => 180,
        'filenameDateFormat' => 'Y-m-d'
    ]
];

// 定义异常处理日志通道
$config['logger']['exception'] = &$config['logger']['primary'];
// 定义redis和pdo异常日志处理通道
$config['logger']['redis'] = $config['logger']['pdo'] = &$config['logger']['exception'];

return $config;
