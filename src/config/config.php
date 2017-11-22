<?php
/**
 * 公共配置文件
 *
 * @author admin
 * @date   2017/6/13 18:18
 */
$config = [];

// 时区配置
$config['timezone'] = 'PRC';

// 语言包 ===> 已移至可写配置文件
//$config['language'] = 'zh';

// 是否开启语言包功能
$config['use-language'] = true;

// blade模板缓存路径，默认'resource/blade-cache'
$config['view.compiled'] = 'resource/blade-cache';
// blade模板路径，默认'resource/views'
$config['view.paths'] = 'resource/views';

// 是否使用blade模板引擎，默认false
$config['use-blade-engine'] = false;

// view version模板版本（如使用blade模板引擎，此参数无效）
$config['view-version'] = 'primary';

// 是否输出控制台调试信息，默认true
$config['response-console-log'] = true;
// 是否输出追踪信息到控制台，默认true
$config['response-trace-log'] = true;

// session配置
$config['session'] = [
    'auto-start' => true, // 实例化Session类时开启session_start
//    'use-trans-sid' => '',
//    'name' => '', // session id
//    'path' => '/', // session path
//    'domain' => '',
//    'expire' => '1440', // 有效期
//    'secure' => '',
//    'httponly' => true,
//    'use-cookies' => true,
//    'cache-limiter' => '',
//    'cache-expire' => '',
//    'driver' => '', // 自定义session处理器
];

// cookie配置
$config['cookie'] = [
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
];


// 设置默认模型类，默认“Lxh\MVC\Model”
//$config['default-model'] = Lxh\MVC\Model::class;

// 增加配置文件
$config['add-config'] = [
    // 数据库配置文件
    __ENV__ . '/db/config',
    __ENV__ . '/app',
];

// 增加配置文件，并使用文件名作为key
$config['add-config-name'] = [
    __ENV__ . '/client-config',
    'mail'
];

// 模块设定, 支持多模块, 多个用数组表示
// 如果设置了多模块, 且没有开启子域名部署, 配置路由的时候默认指向第一个模块, 如需走第二个模块需要在路由配置中使用"module"参数指定
$config['modules'] = ['Admin', 'Client'];

// 开启子域名部署
$config['domain-deploy'] = true;
// 配置子域名指向模块
$config['domain-deploy-config'] = [
    'dev.lxh.com' => 'Admin',
    'www.lxh.com' => 'Client',
    '119.23.229.90' => 'Client',
];

// 记录错误日志级别
$config['record-error-info-level'] = [
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
];

/**
 * 事件监听配置
 *
 * route.dispatch.before 路由匹配前触发
 * route.dispatch.after  路由匹配成功后触发
 * route.auth.success    用户登录验证成功后触发
 * response.send.before  输出内容给浏览器之前触发
 * exception             抛出异常时触发（异常处理事件）
 */
$config['events'] = [
    'route.dispatch.after' => [
    ],
    'response.send.after' => [
        'track'
    ],
    // 异常报告
    'exception.report' => [],
    'route.dispatch.before' => [],
    'route.dispatch.after' => [],

];

// 公共中间件配置（按顺序执行）
$config['middleware'] = [
    '*' => [

    ],
    'Admin' => [
//        Lxh\Admin\Middleware\Globals::class,
    ],
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
