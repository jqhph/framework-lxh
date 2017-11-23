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
use Lxh\MVC\Model;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\Http\Client;
use Lxh\Helper\Util;
use Lxh\Support\Arr;
use Lxh\Support\Collection;
use Lxh\Contracts\Support\Htmlable;

// 常用的变量先注册到全局变量中
$GLOBALS['CONTAINER']     = Container::getInstance();
$GLOBALS['CONFIG']        = $GLOBALS['CONTAINER']->make('config');
$GLOBALS['MODEL_FACTORY'] = $GLOBALS['CONTAINER']->make('model.factory');
$GLOBALS['EVENTS']        = $GLOBALS['CONTAINER']->make('events');
if ($GLOBALS['CONFIG']->get('use-language')) {
    $GLOBALS['LANGUAGE'] = $GLOBALS['CONTAINER']->make('language.manager');
}

$GLOBALS['resource-server']  = $GLOBALS['CONFIG']->get('client-config.resource-server');
$GLOBALS['js-version']       = $GLOBALS['CONFIG']->get('js-version');
$GLOBALS['css-version']      = $GLOBALS['CONFIG']->get('css-version');
$GLOBALS['resource-version'] = $GLOBALS['CONFIG']->get('client-config.resource-version');
$GLOBALS['use-blade-engine'] = $GLOBALS['CONFIG']->get('use-blade-engine');
$GLOBALS['view-version']     = $GLOBALS['CONFIG']->get('view-version', 'v1.0');

/**
 * 获取项目根目录路径
 *
 * @param string $path
 * @return string
 */
function base_path($path = null)
{
    if ($path) {
        return __ROOT__ . $path;
    }
    return __ROOT__;
}

/**
 * 获取data目录路径
 *
 * @param string $path
 * @return string
 */
function data_path($path = null)
{
    if ($path) {
        return __DATA_ROOT__ . $path;
    }
    return __DATA_ROOT__;
}

/**
 * 获取配置文件路径
 *
 * @param string $path
 * @return string
 */
function config_path($path = null)
{
    if ($path) {
        return __ROOT__ . 'config/' . $path;
    }
    return  __ROOT__ . 'config/';
}

/**
 * 获取容器对象
 *
 * @return Container
 */
function container()
{
    return $GLOBALS['CONTAINER'];
}

/**
 * 获取容器实例
 *
 * @return object
 */
function ioc()
{
    return $GLOBALS['CONTAINER'];
}

/**
 * 从容器中获取一个服务
 *
 * @param  string $abstract 服务名称
 * @return object
 */
function resolve($abstract)
{
    return $GLOBALS['CONTAINER']->make($abstract);
}

function make($abstract)
{
    return $GLOBALS['CONTAINER']->make($abstract);
}



/**
 * 获取单例模型
 *
 * @param  string $name
 * @return Model
 */
function model($name = __CONTROLLER__)
{
    return $GLOBALS['MODEL_FACTORY']->get($name);
}

/**
 * 创建一个新的模型
 *
 * @param  string $name
 * @return Model
 */
function create_model($name = __CONTROLLER__)
{
    return $GLOBALS['MODEL_FACTORY']->create($name);
}

/**
 * 事件管理
 *
 * @return Dispatcher
 */
function events()
{
    return $GLOBALS['EVENTS'];
}

/**
 * Get the resolve controller with name
 *
 * @param string $name
 * @return \Lxh\MVC\Controller
 */
function controller($name)
{
    return $GLOBALS['CONTAINER']['controller.manager']->get($name);
}

// 加载js
function load_js($name, $dir = 'js', $module = __MODULE__)
{
    return "<script src=\"{$GLOBALS['resource-server']}/assets/{$GLOBALS['resource-version']}/{$module}/lib/$dir/$name.js?v={$GLOBALS['js-version']}\"></script>";
}

// 加载css
function load_css($name, $dir = 'css', $module = __MODULE__)
{
    return "<link href=\"{$GLOBALS['resource-server']}/assets/{$GLOBALS['resource-version']}/{$module}/$dir/$name.css?v={$GLOBALS['css-version']}\" rel=\"stylesheet\" type=\"text/css\" />";
}

// 加载图片
function load_img($name, $dir = 'images', $module = __MODULE__)
{
    return "{$GLOBALS['resource-server']}/assets/{$GLOBALS['resource-version']}/{$module}/$dir/$name";
}

/**
 * 获取语言包管理对象
 *
 * @return Manager
 */
function language()
{
    return $GLOBALS['LANGUAGE'];
}

/**
 * @return FileManager
 */
function files()
{
    return $GLOBALS['CONTAINER']->make('files');
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
    return $GLOBALS['LANGUAGE']->translate($label, $category, $sprints);
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
    return $GLOBALS['LANGUAGE']->translateWithGolobal($label, $category, $sprints);
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
    return $GLOBALS['LANGUAGE']->translateOption($value, $field);
}

/**
 * 获取用户信息管理对象
 *
 * @return Model
 */
function user()
{
    static $instance = null;

    if ($instance) return $instance;

    return $instance = $GLOBALS['CONTAINER']->make('model.factory')->create('User');

}


/**
 * 获取用户信息管理对象
 *
 * @return Model
 */
function admin()
{
    static $instance = null;

    if ($instance) return $instance;

    return $instance = $GLOBALS['CONTAINER']->make('model.factory')->create('Admin');

}

/**
 * Http客户端
 *
 * @return Client
 */
function http()
{
    return make('http.client');
}

/**
 * 获取日志通道实例
 *
 * @param  string $channel 日志通道名称
 * @return LoggerManager
 */
function logger($channel = 'primary')
{
    return $GLOBALS['CONTAINER']->make('logger')->channel($channel);
}

// 获取request参数
function I($name = null, $default = null, $isEmpty = false)
{
    if ($name === null) return file_get_contents('php://input');

    if ($isEmpty) return empty($_REQUEST[$name]) ? $default : $_REQUEST[$name];

    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

/**
 * Call the given Closure with the given value then return the value.
 *
 * @param  mixed  $value
 * @param  callable|null  $callback
 * @return mixed
 */
function tap($value, $callback = null)
{
    $callback($value);

    return $value;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
    return $value instanceof Closure ? $value() : $value;
}

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed   $target
 * @param  string|array  $key
 * @param  mixed   $default
 * @return mixed
 */
function data_get($target, $key, $default = null)
{
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', $key);

    while (! is_null($segment = array_shift($key))) {
        if ($segment === '*') {
            if ($target instanceof Collection) {
                $target = $target->all();
            } elseif (! is_array($target)) {
                return value($default);
            }

            $result = Arr::pluck($target, $key);

            return in_array('*', $key) ? Arr::collapse($result) : $result;
        }

        if (Arr::accessible($target) && Arr::exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target) && isset($target->{$segment})) {
            $target = $target->{$segment};
        } else {
            return value($default);
        }
    }

    return $target;
}

/**
 * Create a collection from the given value.
 *
 * @param  mixed  $value
 * @return Collection
 */
function collect($value = null)
{
    return new Collection($value);
}

/**
 * Get all of the given array except for a specified array of items.
 *
 * @param  array  $array
 * @param  array|string  $keys
 * @return array
 */
function array_except($array, $keys)
{
    return Arr::except($array, $keys);
}

/**
 * Get the class "basename" of the given object / class.
 *
 * @param  string|object  $class
 * @return string
 */
function class_basename($class)
{
    $class = is_object($class) ? get_class($class) : $class;

    return basename(str_replace('\\', '/', $class));
}

/**
 * Escape HTML special characters in a string.
 *
 * @param  Htmlable|string  $value
 * @return string
 */
function e($value)
{
    if ($value instanceof Htmlable) {
        return $value->toHtml();
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Add a piece of shared data to the environment.
 *
 * @param  array|string  $key
 * @param  mixed  $value
 * @return mixed
 */
function share_var($key, $value = null)
{
    return $GLOBALS['CONTAINER']['view.adaptor']->share($key, $value);
}

/**
 * Get the evaluated view contents for the given view.
 *
 * @param  string  $view
 * @param  array   $data
 * @return \Lxh\Contracts\View\View
 */
function view($view, array $data = []) {
    return $GLOBALS['CONTAINER']['view.adaptor']->make($view, $data);
}

function render_view($view, array $data = [])
{
    return $GLOBALS['CONTAINER']['view.adaptor']->make($view, $data)->render();
}

/**
 * 手动调用控制器接口
 *
 * @return mixed
 */
function call($controller, $action, array $params = [])
{
    return $GLOBALS['CONTAINER']->make('controller.manager')->call($controller, $action, $params);
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
 * @return mixed
 */
function config($key = null, $default = null)
{
    return $GLOBALS['CONFIG']->get($key, $default);
}

function is_cli()
{
    return PHP_SAPI == 'cli';
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

    if (isset($instances[$name])) return $instances[$name];

    return $instances[$name] = new PDO(config("db.$name"));
}

/**
 * @return Query
 */
function query($name = 'primary')
{
    $q = $GLOBALS['CONTAINER']->make('query');
    // 设置连接类型
    return $q->setConnectionName($name);
}

/**
 * 从数组中获取一个参数
 *
 * @param  array $data
 * @param  string|int $key
 * @param  mixed $default
 * @return mixed
 */
function get_value(& $data, $key, $default = null)
{
    return isset($data[$key]) ? $data[$key] : $default;
}

function get_isset(& $data, $key, $default = null)
{
    return isset($data[$key]) ? $data[$key] : $default;
}

function get_not_empty(& $data, $key, $default = null)
{
    return !empty($data[$key]) ? $data[$key] : $default;
}

/**
 * Retry an operation a given number of times.
 *
 * @param  int  $times
 * @param  callable  $callback
 * @param  int  $sleep
 * @return mixed
 *
 * @throws \Exception
 */
function retry($times, callable $callback, $sleep = 0)
{
    $times--;

    beginning:
    try {
        return $callback();
    } catch (Exception $e) {
        if (! $times) {
            throw $e;
        }

        $times--;

        if ($sleep) {
            usleep($sleep * 1000);
        }

        goto beginning;
    }
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
    return $GLOBALS['CONTAINER']->make('track')->record($name, $options, $save);
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
    return $GLOBALS['CONTAINER']->make('track')->record($name, $options, $save);
}

// 追踪数据库信息
function db_track(& $sql, & $time, $type = 'unknown', array & $params = [])
{
    if (is_prod()) {
        return false;
    }
    return $GLOBALS['CONTAINER']
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
    $s  = '<span style="color:#e07c79">';
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
    echo "</pre><br/>";
}

function dd(...$args)
{
    foreach ($args as $x) {
        debug($x);
    }
}

function ddd(...$args) {
    dd(...$args);
    die;
}
