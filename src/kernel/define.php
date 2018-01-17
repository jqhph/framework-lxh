<?php
/**
 * 系统常量定义
 *
 * @author Jqh
 * @date   2018/1/14 11:25
 */

// 定义路径常量
define('__ROOT__', dirname(__DIR__) . '/');
define('__APP__', __ROOT__ . 'application/');
define('__CONFIG__', __ROOT__ . 'config/');
define('__RESOURCE__', __ROOT__ . 'resource/');
define('__LANGUAGE__', __ROOT__ . 'resource/language/');
define('__VIEW__', __ROOT__ . 'resource/views/');

// 环境常量
define('ENV_TEST', 'test'); // 测试环境
define('ENV_DEV', 'dev');   // 开发环境
define('ENV_PROD', 'prod'); // 生产环境

// 事件常量
/**
 * 路由调度之前触发
 *
 * 不接受任何参数
 */
define('EVENT_ROUTE_DISPATCH_BEFORE', 'route.dispatch.before');

/**
 * 路由调度之后触发
 *
 * @param array $requestParams 路由解析后的请求参数
 */
define('EVENT_ROUTE_DISPATCH_AFTER', 'route.dispatch.after');

/**
 * 程序发生异常时触发
 *
 * @param Exception $e
 */
define('EVENT_EXCEPTION', 'exception');

/**
 * 监听此事件设置异常显示界面
 * 提示：可以根据普通请求和ajax请求返回不信息（界面）给用户
 *
 * @param Exception $e
 */
define('EVENT_EXCEPTION_REPORT', 'exception.report');

/**
 * 用户身份鉴权成功后触发（如果允许免登录访问，也默认为鉴权成功）
 *
 */
define('EVENT_AUTH_SUCCESS', 'auth.success');

/**
 * 用户控制器action执行完毕，输出内容之前
 *
 */
define('EVENT_RESPONSE_BEFORE', 'response.send.before');

/**
 * 用户控制器action执行完毕，输出内容之后
 *
 */
define('EVENT_RESPONSE_AFTER', 'response.send.after');
