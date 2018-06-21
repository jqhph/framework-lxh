<?php
/**
 * 控制器父类
 *
 * @author Jqh
 * @date   2017/6/14 10:27
 */

namespace Lxh\Mvc;

use Lxh\Admin\Admin;
use Lxh\Admin\Layout\Content;
use Lxh\Application;
use Lxh\Config\Config;
use Lxh\Contracts\Container\Container;
use Lxh\Events\Dispatcher;
use Lxh\Helper\Util;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Mvc\ControllerManager;
use Lxh\Mvc\Model;
use Lxh\Template\View;
use Lxh\View\Factory;
use Lxh\Session\Store as Session;
use Lxh\Cookie\Store as Cookie;

abstract class Controller
{
    const SUCCESS = 10001;
    const FAILED  = 10002;
    // 参数错误
    const INVALID_ARGUMENTS = 10003;

    // 用户身份鉴权失败
    const NOT_AUTH = 10008;

    /**
     * 控制器名称
     *
     * @var string
     */
    protected $name;

    /**
     * 模块名称
     *
     * @var string
     */
    protected $module;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var ControllerManager
     */
    protected $manager;

    /**
     * 控制器中间件
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * @var mixed
     */
    private $currentMiddleware;

    /**
     * @var Container;
     */
    protected $container;

    public function __construct($name = null, Container $container = null, ControllerManager $manager = null)
    {
        $this->name      = $name ?: $this->parseName();
        $this->container = $container ?: Application::$container;
        $this->manager   = $manager ?: $this->container['controllerManager'];
        $this->request   = request();
        $this->response  = response();
        $this->module    = __MODULE__;

        // 触发控制器被实例化事件
        fire('controller.' . $this->getLowerCaseDashName() . '.resolving', [$this]);

        // 初始化
        $this->initialize();
    }

    /**
     * @return mixed
     */
    protected function parseName()
    {
        $names = explode('\\', __CLASS__);

        return end($names);
    }

    /**
     * @return string
     */
    protected function getLowerCaseDashName()
    {
        return slug($this->name);
    }


    /**
     * 初始化操作
     *
     * @return void
     */
    protected function initialize()
    {
    }

    /**
     *
     * @return Admin
     */
    protected function admin()
    {
        return $this->container['admin'];
    }

    /**
     * @return Content
     */
    protected function content()
    {
        return $this->container['admin']->content();
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function share($key, $value = null)
    {
        return $this->container['viewAdaptor']->share($key, $value);
    }

    /**
     * Get the rendered content of the view based
     *
     * @param  string  $view
     * @param  array   $data
     * @param  bool    $usePrefix
     * @return string
     */
    protected function render($view, array $data = [], $usePrefix = true)
    {
        return $this->container['viewAdaptor']->make($view, $data, $usePrefix)->render();
    }

    /**
     * 是否输出控制台调试信息
     *
     * @param  bool $flag
     * @return static
     */
    public function withConsoleOutput($flag = true)
    {
        $this->response->withConsoleOutput($flag);
        return $this;
    }

    /**
     * 创建一个模型
     *
     * @param  string $name 模型名称
     * @return Model
     */
    protected function model($name = null)
    {
        return $this->container['modelFactory']->create($name);
    }

    /**
     * 调用控制器接口
     *
     * @return mixed
     */
    protected function call($controller, $action)
    {
        return $this->manager->call($controller, $action);
    }

    /**
     * 设置中间件
     *
     * $this->middleware('test');
     *
     * only   => test1   只有 actionTest1方法会执行此中间件
     * except => test1   除了actionTest1方法外的所有方法都会执行此中间件
     *
     * @param string $middleware 中间件名称
     * @param array  $options
     * @return static
     */
    protected function middleware($middleware)
    {
        $this->currentMiddleware = $middleware;

        $this->middleware[$middleware] = [];

        return $this;
    }

    /**
     * 只有触发$methods方法中间件才会执行
     *
     * @param  $methods array|string action方法
     * @return static
     */
    protected function only($methods)
    {
        $this->middleware[$this->currentMiddleware]['only'] = $methods;

        return $this;
    }

    /**
     * 除了$methods方法外所有其他action方法都会中心中间件
     *
     * @param  $methods array|string action方法
     * @return static
     */
    protected function except($methods)
    {
        $this->middleware[$this->currentMiddleware]['except'] = $methods;

        return $this;
    }

    /**
     * 获取控制器中间件
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * 获取字段验证处理器
     * 用法清请参考：https://github.com/vlucas/valitron
     *
     *  $v->fill(['name' => '张三', 'email' => 'jqh@163.com'])
        ->rule('required', array('name', 'email'))
        ->rule('email', 'email');

        if ($v->validate()) {
            echo "Yay! We're all good!<br>";
        } else {
            // Errors
            debug($v->errors());
        }
     *
     * @return Validator
     */
    protected function validator(array $input = [], array $rules = [])
    {
        $v = $this->container['validator']->fill($input);

        if ($rules) {
            $v->rules($rules);
        }

        return $v;
    }

    /**
     * 返回成功信息
     *
     * @return array
     */
    protected function success($msg = 'Succeeded', array $options = [])
    {
        return $this->message($msg, static::SUCCESS, $options);
    }

    /**
     * 返回失败信息
     *
     * @return array
     */
    protected function failed($msg = 'Failed', array $options = [])
    {
        return $this->message($msg, static::FAILED, $options);
    }

    /**
     * 返回错误信息
     *
     * @return array
     */
    protected function error($msg = 'Invalid arguments', $status = self::INVALID_ARGUMENTS)
    {
        return $this->message($msg, $status);
    }

    /**
     * 返回数据到web
     *
     * @return array
     */
    protected function message($msg, $status, array $options = [])
    {
        if (is_array($msg)) {
            return ['status' => & $status] + $msg;
        }
        return (['status' => & $status, 'msg' => & $msg] + $options);
    }

    /**
     * 从服务容器中获取服务实例
     * 并设置为控制器属性
     *
     * @param string $name 小驼峰写法会自动转化为“.”格式，如：
     *               httpRequest => request
     *
     * @return object
     */
    public function __get($name)
    {
        return $this->$name = $this->container[$name];
    }
}
