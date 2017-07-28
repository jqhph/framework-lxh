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
use Lxh\Language\Manager;
use Lxh\File\FileManager;
use Lxh\Logger\Manager as LoggerManager;
use Lxh\Config\Config;
use Lxh\ORM\Query;

// 常用的变量先注册到全局变量中
$GLOBALS['__container__'] = Container::getInstance();
$GLOBALS['__config__']    = $GLOBALS['__container__']->make('config');
$GLOBALS['__language__']  = $GLOBALS['__container__']->make('language.manager');

$GLOBALS['resource-server']  = $GLOBALS['__config__']->get('client-config.resource-server');
$GLOBALS['js-version']       = $GLOBALS['__config__']->get('js-version');
$GLOBALS['css-version']      = $GLOBALS['__config__']->get('css-version');
$GLOBALS['resource-version'] = $GLOBALS['__config__']->get('client-config.resource-version');

/**
 * 获取容器对象
 *
 * @return Container
 */
function container()
{
    return $GLOBALS['__container__'];
}

/**
 * 获取一个服务
 *
 * @param  string $abstract 服务名称
 * @return object
 */
function make($abstract)
{
    return $GLOBALS['__container__']->make($abstract);
}

// 加载js
function load_js($name, $dir = 'js')
{
    echo "<script src=\"{$GLOBALS['resource-server']}/static/{$GLOBALS['resource-version']}/lib/$dir/$name.js?v={$GLOBALS['js-version']}\"></script>";
}

// 加载css
function load_css($name, $dir = 'css')
{
    echo "<link href=\"{$GLOBALS['resource-server']}/static/{$GLOBALS['resource-version']}/$dir/$name.css?v={$GLOBALS['css-version']}\" rel=\"stylesheet\" type=\"text/css\" />";
}

// 加载图片
function load_img($name, $dir = 'images')
{
    echo "{$GLOBALS['resource-server']}/static/{$GLOBALS['resource-version']}/$dir/$name";
}

/**
 * 获取语言包管理对象
 *
 * @return Manager
 */
function language()
{
    return $GLOBALS['__language__'];
}

/**
 * @return FileManager
 */
function file_manager()
{
    return $GLOBALS['__container__']->make('file.manager');
}

/**
 * Translate label/labels
 *
 * @param  string $label name of label
 * @param  string $category
 * @param  mixed $default
 * @param  array $sprints format fields array
 * @return string | array
 */
function trans($label, $category = 'labels', array $sprints = [])
{
    return $GLOBALS['__language__']->translate($label, $category, $sprints);
}

/**
 * 使用全局语言包翻译
 *
 * @param  string $label 需要翻译的名称
 * @param  string $category 翻译的类型
 * @param  array  $sprints 需要插入到格式化翻译字符的参数
 * @return string
 */
function trans_with_global($label, $category = 'labels', array $sprints = [])
{
    return $GLOBALS['__language__']->translateWithGolobal($label, $category, $sprints);
}

/**
 * 选项翻译
 *
 * @param  string|int $value 选项值
 * @param  string     $field 选项名称
 * @return string|int
 */
function trans_option($value, $field)
{
    return $GLOBALS['__language__']->translateOption($value, $field);
}

function ucfirst_trans($label, $category = 'labels')
{
    return ucfirst($GLOBALS['__language__']->translate($label, $category));
}

function ucfirst_trans_with_global($label, $category = 'labels')
{
    return ucfirst($GLOBALS['__language__']->translateWithGolobal($label, $category));
}

function ucfirst_trans_option($value, $label)
{
    return ucfirst($GLOBALS['__language__']->translateOption($value, $label));
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

/**
 * 获取日志通道实例
 *
 * @param  string $channel 日志通道名称
 * @return LoggerManager
 */
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

/**
 * 获取模板内容
 *
 * @param  string $action
 * @param  string $controller
 * @param  array  $vars 要传递到模板的值，只有当前模板可以用
 * @return string
 */
function fetch_view($action = __ACTION__, $controller = __CONTROLLER__, array $vars = [])
{
   return $GLOBALS['__container__']->make('view')->fetch("$controller/$action", $vars);
}

/**
 * 获取组件模板内容
 *
 * @param  string $name 组件模板路径
 * @param  array  $vars 要传递到模板的值，只有当前模板可以用
 * @return void
 */
function component_view($name, array $vars = [])
{
    return $GLOBALS['__container__']->make('view')->fetch("component/$name", $vars);
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

/**
 * 分配变量到模板输出
 * 通过此方法分配的变量所有引入的模板都可用
 *
 * @param  string $key  在模板使用的变量名称
 * @param  mixed $value 变量值，此处使用引用传值，分配时变量必须先定义
 * @return void
 */
function assign($key, $value)
{
    $GLOBALS['__container__']->make('view')->assign($key, $value);
}

/**
 * 返回完整的模板（包括header和footer）
 *
 * @param  string $action
 * @param  array  $vars 要传递到模板的值，只有当前模板可以用
 * @return string
 */
function fetch_complete_view($action = __ACTION__, array $vars = [])
{
    $controller = __CONTROLLER__;

    $view = $GLOBALS['__container__']->make('view');

    return $view->fetch('Public/header') . $view->fetch("$controller/$action", $vars) . $view->fetch('Public/footer');
}

/**
 * 是否是开发环境
 *
 * @return bool
 */
function is_dev()
{
    return __ENV__ == ENV_DEV;
}

/**
 * 是否是测试环境
 *
 * @return bool
 */
function is_test()
{
    return __ENV__ == ENV_TEST;
}

/**
 * 是否是生产环境
 *
 * @return bool
 */
function is_prod()
{
    return __ENV__ == ENV_PROD;
}

/**
 * 获取配置文件参数代理函数
 *
 * @return mixed | Config
 */
function config($key = null, $default = null)
{
    return $GLOBALS['__config__']->get($key, $default);
}

/**
 * 获取pdo连接实例
 *
 * @param  string $name 对应配置文件数据库配置键名
 * @return PDO
 */
function pdo($name = 'primary')
{
    static $instances = [];

    if (isset($instances[$name])) {
        return $instances[$name];
    }

    return $instances[$name] = new PDO(config("db.$name"));
}

/**
 * @return Query
 */
function query($name = 'primary')
{
    static $instances = [];

    if (isset($instances[$name])) {
        return $instances[$name];
    }

    $q = $GLOBALS['__container__']->make('query');
    // 设置连接类型
    $q->connection($name);
    return $instances[$name] = $q;
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
