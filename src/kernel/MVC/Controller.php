<?php
/**
 * 控制器父类
 *
 * @author Jqh
 * @date   2017/6/14 10:27
 */

namespace Lxh\MVC;

use Lxh\Admin\Admin;
use Lxh\Config\Config;
use Lxh\Contracts\Container\Container;
use Lxh\Events\Dispatcher;
use Lxh\Helper\Util;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\MVC\ControllerManager;
use Lxh\MVC\Model;
use Lxh\Template\View;
use Lxh\View\Factory;
use Lxh\Session\Store as Session;
use Lxh\Cookie\Store as Cookie;

abstract class Controller
{
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

    public function __construct($name, Container $container, ControllerManager $manager)
    {
        $this->name = $name;
        $this->container = $container;
        $this->manager = $manager;

        $this->module = __MODULE__;

        // 初始化
        $this->initialize();
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
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function share($key, $value = null)
    {
        return $this->container['view.adaptor']->share($key, $value);
    }

    /**
     * Get the rendered content of the view based
     *
     * @param  string  $view
     * @param  array   $data
     * @param  bool    $compalete
     * @return string
     */
    protected function render($view, array $data = [], $compalete = false)
    {
        $factory = $this->container['view.adaptor'];

        if ($compalete) {
            return $factory->make('public.header')->render()
                 . $factory->make($view, $data)->render()
                 . $factory->make('public.footer')->render();
        }
        return $factory->make($view, $data)->render();

    }

    /**
     * 是否输出控制台调试信息
     *
     * @param  bool $flag
     * @return static
     */
    public function withConsoleOutput($flag = true)
    {
        $this->container['http.response']->withConsoleOutput($flag);
        return $this;
    }

    /**
     * 创建一个模型
     *
     * @param  string $name 模型名称
     * @return Model
     */
    protected function createModel($name = __CONTROLLER__)
    {
        return $this->container['model.factory']->create($name);
    }

    /**
     * 创建一个模型
     *
     * @param  string $name 模型名称
     * @return Model
     */
    protected function model($name = __CONTROLLER__)
    {
        return $this->container['model.factory']->$name;
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
     * 从服务容器中获取服务实例
     * 并设置为控制器属性
     *
     * @param string $name 小驼峰写法会自动转化为“.”格式，如：
     *               httpRequest => http.request
     *
     * @return object
     */
    public function __get($name)
    {
        return $this->$name = $this->container[$name];
    }
}
