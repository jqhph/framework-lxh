<?php
/**
 * 公共函数
 *
 * @author Jqh
 * @date   2017/6/13 18:14
 */

use Lxh\Container\Container;
use Lxh\ORM\Connect\PDO;
use Lxh\Helper\Console;

$GLOBALS['__container__'] = Container::getInstance();

/**
 * 服务容器代理函数
 *
 * @param  string $abstract 服务名称
 * @return instance | Container
 */
function container($abstract = null)
{
    return $abstract ? $GLOBALS['__container__']->make($abstract) : $GLOBALS['__container__'];
}

function get_db()
{

}

// 获取用户信息管理对象
function user()
{
    static $instance = null;

    if ($instance) {
        return $instance;
    }

    return $instance = $GLOBALS['__container__']->make('model.factory')->get('User');

}

function logger($channel = 'exception')
{
    return $GLOBALS['__container__']->make('logger')->channel($channel);
}

// 获取request参数
function I($name = null, $default = null, $isEmpty = false)
{
    if (! $name) {
        return file_get_contents('php://input');
    }

    if ($isEmpty) {
        return empty($_REQUEST[$name]) ? $default : $_REQUEST[$name];
    }

    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function redirect_404($msg = null)
{

}

// 获取模板内容并输出
function fetch_view($action = __ACTION__, $controller = __CONTROLLER__, array $vars = [])
{
   return $GLOBALS['__container__']->make('view')->fetch("$controller/$action", $vars);
}

// 输出模板内容
function display_view($action = __ACTION__, $controller = __CONTROLLER__, array $vars = [])
{
    return $GLOBALS['__container__']->make('view')->display("$controller/$action", $vars);
}

/**
 * 手动调用控制器接口
 *
 * @return mixed
 */
function call($controller, $action, array $params = [])
{
    return $GLOBALS['__container__']->make('controller.manager')->call($controller, $action, $params);
}

// 分配变量到模板
function assign($key, $value)
{
    $GLOBALS['__container__']->make('view')->assign($key, $value);
}

// 返回完整的模板
function fetch_complete_view($action = __ACTION__, $controller = __CONTROLLER__, array $vars = [])
{
    $view = $GLOBALS['__container__']->make('view');

    return $view->fetch('Public/header') . $view->fetch("$controller/$action") . $view->fetch('Public/footer');
}

function Q()
{
    return $GLOBALS['__container__']->make('query');
}

function is_dev()
{
    return __ENV__ == ENV_DEV;
}

function is_test()
{
    return __ENV__ == ENV_TEST;
}

function is_prod()
{
    return __ENV__ == ENV_PROD;
}

/**
 * 获取配置文件参数代理函数
 *
 * @param
 * @return void
 */
function config($key = null, $default = null)
{
    return $key ? $GLOBALS['__container__']->make('config')->get($key, $default) : $GLOBALS['__container__']->make('config');
}

/**
 * @return Lxh\ORM\Connect\PDO
 */
function pdo()
{
    return $GLOBALS['__container__']->make('pdo');
}

/**
 * @return Lxh\ORM\Query
 */
function query()
{
    return $GLOBALS['__container__']->make('query');
}

/**
 * 从数组中获取一个参数
 *
 * @param  array $data
 * @param  string|int $key
 * @param  mixed $default
 * @return mixed
 */
function get_value(array & $data, $key, $default = null)
{
    return isset($data[$key]) ? $data[$key] : $default;
}

/**
 * 记录追踪信息
 *
 * @param string $name    名称
 * @param mixed  $options 记录具体内容， 基本格式
 *                  [
 *                      'command' => 'test',    // 要记录的命令，不可为空
 *                      'start' => microtime(true), // 开始时间
 *                      'type' => 'w',          // 类型，自定义，可为空
 *                      'params' => []          // 其余参数，自定义，可为空
 *                  ]
 * @param bool   $save    是否持久化存储
 * @return mixed
 */
function track($name, $options = '', $save = false)
{
    return $GLOBALS['__container__']->make('track')->record($name, $options, $save);
}

/**
 * 记录调试用追踪信息，此函数在生产环境无效
 *
 * @param string $name    名称
 * @param mixed  $options 记录具体内容， 基本格式
 *                  [
 *                      'command' => 'test',    // 要记录的命令，不可为空
 *                      'start' => microtime(true), // 开始时间
 *                      'type' => 'w',          // 类型，自定义，可为空
 *                      'params' => []          // 其余参数，自定义，可为空
 *                  ]
 * @param bool   $save    是否持久化存储
 * @return mixed
 */
function debug_track($name, $options = '', $save = false)
{
    if (is_prod()) {
        return false;
    }
    return $GLOBALS['__container__']->make('track')->record($name, $options, $save);
}

// 追踪数据库信息
function db_track(& $sql, & $time, $type = 'unknown', array & $params = [])
{
    if (is_prod()) {
        return false;
    }
    return $GLOBALS['__container__']
        ->make('track')
        ->record('db', [
            'command' => & $sql,
            'type' => $type,
            'start' => & $time,
            'params' => & $params
    ]);
}

/**
 * 输出内容到控制台，在生产环境无效
 * console.log
 *
 * @return void
 */
function console_log()
{
    return call_user_func_array([Console::class, 'log'], func_get_args());
}

/**
 * console.info
 *
 * @return void
 */
function console_info()
{
    return call_user_func_array([Console::class, 'info'], func_get_args());
}

/**
 * console.warn
 *
 * @return void
 */
function console_warn()
{
    return call_user_func_array([Console::class, 'warn'], func_get_args());
}

/**
 * console.error
 *
 * @return void
 */
function console_error()
{
    return call_user_func_array([Console::class, 'error'], func_get_args());
}

/**
 * console.table
 *
 * @return void
 */
function console_table()
{
    return call_user_func_array([Console::class, 'table'], func_get_args());
}

// 输出调试内容到浏览器
function debug($data, $print = true, $json = false)
{
    echo '<pre>';
    $s  = '<span style="color:#66ccff">';
    $se = '</span>';
    if (is_string($data) || is_bool($data) || is_float($data) || is_integer($data)) {
        echo $s . date('[H:i:s]') . $se .  " $data<br/>";
        return;
    }

    if ($json) {
        return debug(json_encode($data));
    }
    if ($print) {
        print_r($data);
    } else {
        var_dump($data);
    }
    echo "<br/><br/>";

}

// 用于测试性能
function nature_test(callable $call, $times = 15000)
{
    $s = microtime(true);
    for ($i = 0; $i < $times; $i++) {
        $call();
    }
    return microtime(true) - $s;
}
