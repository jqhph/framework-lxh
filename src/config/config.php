<?php
/**
 * 公共配置文件
 *
 * @author admin
 * @date   2017/6/13 18:18
 */
$config = [];

// 语言包 ===> 已移至可写配置文件
//$config['language'] = 'zh';

// 模板缓存路径，默认'resource/blade-cache'
$config['view.compiled'] = 'resource/blade-cache';
// 模板路径，默认'resource/views'
$config['view.paths'] = 'resource/views';

// view version模板版本
$config['view-version'] = 'v1.0';

// 是否输出控制台调试信息，默认true
$config['response-console-log'] = true;
// 是否输出追踪信息到控制台，默认true
$config['response-trace-log'] = true;

// 设置默认模型类，默认“Lxh\MVC\Model”
//$config['default-model'] = Lxh\MVC\Model::class;

// 增加配置文件
$config['add-config'] = [
    // 数据库配置文件
    __ENV__ . '/db/config',
    __ENV__ . '/app',
    __ENV__ . '/home',
    __ENV__ . '/ucenter',
];

// 增加配置文件，并使用文件名作为key
$config['add-config-name'] = [
    __ENV__ . '/client-config',
];

// 模块设定, 支持多模块, 多个用数组表示
// 如果设置了多模块, 且没有开启子域名部署, 配置路由的时候默认指向第一个模块, 如需走第二个模块需要在路由配置中使用"module"参数指定
$config['modules'] = ['Admin', 'Home', 'Ucenter'];

// 开启子域名部署
$config['domain-deploy'] = true;
// 配置子域名指向模块，已移至app.php配置文件
//$config['domain-deploy-config'] = [
//    'wo.suitshe.com'   => 'Ucenter',
//    'new.suitshe.com'   => 'Home',
//    'dev.lxh.com' => 'Admin',
//];

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
    ]
];

// 公共中间件配置（按顺序执行）
$config['middleware'] = [
    '*' => [

    ],
    'Home' => [
        Lxh\Home\Middleware\Globals::class,
    ],
];

// 日志配置
$config['logger'] = [
    'exception' => [
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

return $config;
