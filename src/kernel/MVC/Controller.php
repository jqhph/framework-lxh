<?php
/**
 * 控制器父类
 *
 * @author Jqh
 * @date   2017/6/14 10:27
 */

namespace Lxh\MVC;

use Lxh\Contracts\Container\Container;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\MVC\ControllerManager;
use Lxh\MVC\Model;
use Lxh\Template\View;

abstract class Controller
{
    /**
     * 控制器名称
     *
     * @var string
     */
    protected $name;

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
    protected $currentMiddleware;

    /**
     * @var Container;
     */
    protected $container;

    public function __construct()
    {
    }

    /**
     * View
     *
     * @return View
     */
    protected function view()
    {
        return make('view');
    }

    /**
     * @return Request
     */
    public function request()
    {
        return make('http.request');
    }

    /**
     * @return Response
     */
    public function response()
    {
        return make('http.response');
    }

    /**
     * 是否输出控制台调试信息
     *
     * @param  bool $flag
     * @return static
     */
    public function withConsoleOutput($flag = true)
    {
        $this->response()->withConsoleOutput($flag);
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
        return make('model.factory')->create($name);
    }

    /**
     * 创建一个模型
     *
     * @param  string $name 模型名称
     * @return Model
     */
    protected function getModel($name = __CONTROLLER__)
    {
        return make('model.factory')->get($name);
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

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function setControllerName($name)
    {
        $this->name = $name;
    }

    public function setManager(ControllerManager $manager)
    {
        $this->manager = $manager;
    }
}
