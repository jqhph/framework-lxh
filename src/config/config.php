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
        Lxh\Plugins\System\Provider::class,
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

    // 增加配置文件，键值使用字符串则会默认为配置数组键值
    // 如 'mail' => 'mail',  则会用 'mail' 作为键值包含 'mail.php' 文件里面的数组
    'add-config' => [
        // 数据库配置文件
        'db' => __ENV__ . '/db/config',
        'client' => __ENV__ . '/client',
        'app' => __ENV__ . '/app',
        'mail' => 'mail',
        'admin' => 'admin',
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
            'track'
        ],
        // 异常报告
        EVENT_EXCEPTION_REPORT => [],
        EVENT_ROUTE_DISPATCH_BEFORE => [],
        EVENT_ROUTE_DISPATCH_AFTER => [],
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
                'handler' 	=> 'DaysFileHandler',
                'formatter' => 'TextFormatter',
                'level' 	=> '100'
            ]
        ],
        'maxFiles' => 180,
        'filenameDateFormat' => 'Y-m-d'
    ]
];

// 定义异常处理日志通道
$config['logger']['exception'] = & $config['logger']['primary'];
// 定义redis和pdo异常日志处理通道
$config['logger']['redis'] = $config['logger']['pdo'] = & $config['logger']['exception'];

return $config;
