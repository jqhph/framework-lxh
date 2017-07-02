<?php
/**
 * 控制器管理
 *
 * @author Jqh
 * @date   2017/6/14 10:19
 */

namespace Lxh\MVC;

use Lxh\Contracts\Router;
use Lxh\Exceptions\NotFound;
use Lxh\Basis\Factory;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Pipeline;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\MVC\Controller;
use Symfony\Component\Console\Exception\RuntimeException;

class ControllerManager extends Factory
{
    protected $defaultController = 'Index';// 默认控制器
    protected $defaultAction = 'Index';// 默认方法
    protected $module;
    protected $folder;
    protected $controllerName;
    protected $actionName;

    /**
     * 是否初次加载
     *
     * @var bool
     */
    protected $first = true;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Pipeline
     */
    protected $pipeline;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * 中间件数组
     *
     * @var array
     */
    protected $middlewares = [];

    /**
     * 用户登录验证类，默认开启验证
     * false表示不验证
     *
     * @var string | bool
     */
    protected $authParams = 'User';// 登录验证相关信息

    protected $requestParams;// 请求参数

    /**
     * 认证类实例
     *
     * @var object
     */
    protected $auth;

    /**
     * 当前控制器实例
     *
     * @var object
     */
    protected $currentContr;

    /**
     * 控制器实例集合
     *
     * @var array
     */
    protected $contrs;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->request = $container->make('http.request');
        $this->response = $container->make('http.response');

        $this->pipeline = $container->make('pipeline');

        $this->events = $container->make('events');
    }

    /**
     * 分派控制器并调用action
     * */
    public function handle(Router $router)
    {
        $this->setControllerName($router->controllerName);
        $this->setActionName($router->actionName);
        $this->setRequestParams($router->requestParams);
        $this->setAuthParams($router->auth);
        $this->setModule($router->module);
        $this->setFolder($router->folder);

        $this->response->data = $this->call($this->controllerName, $this->actionName, $this->requestParams);

        $this->first = false;
    }

    /**
     * 调用控制器接口
     *
     * @param  string $controller
     * @param  string $action
     * @paran  array  $params
     * @return mixed
     */
    public function call($controller = null, $action = null, $params = [])
    {
        // 获取控制器
        $contr = $this->get($controller);

        $action = $this->getActionMethod($action);

        // 检测action是否存在
        if (! method_exists($contr, $action)) {
            throw new NotFound("Call to undefined method " . get_class($contr) . "::{$action}().", false);
        }

        // 分派控制器前中间件
        $middleware = [];

        if ($this->first) {
            $this->events->fire('route.dispatch.after', [$this->request, $this->response, $params]);

            // 添加公共中间件
            $this->addMiddleware($middleware);
        }

        // 添加控制器中间件
        $this->getContrMiddleware($middleware, $contr);

        return $this->pipeline
                ->send(['req' => $this->request, 'resp' => $this->response, 'params' => & $params])
                ->through($middleware)
                ->then(function () use ($contr, $action, $params) {

                    if ($this->first) {
                        $this->events->fire('route.auth.success', [$this->request, $this->response, $this->requestParams]);

                        // 注册当前控制器
                        $this->container->instance('controller', $contr);
                    }

                    return $contr->$action($this->request, $this->response, $params);
                });
    }

    /**
     * 获取action方法
     *
     * @param  string $action
     * @return string
     */
    public function getActionMethod($action = null)
    {
        return 'action' . ($action ?: $this->actionName);
    }

    /**
     * 添加中间件
     *
     * @return void
     */
    protected function addMiddleware(array & $middleware)
    {
        if ($this->authParams) {
            $middleware[] = $this->getAuth();
        }

        foreach ((array) config('middleware') as $module => & $mid) {
            if ($module == '*' || $module == $this->module) {
                $middleware = array_merge($middleware, (array) $mid);
            }
        }
    }

    /**
     * 获取当前执行的控制器
     *
     * @return Controller
     */
    public function current()
    {
        if (! $this->currentContr) {
            $this->currentContr = $this->get($this->controllerName);
        }

        return $this->currentContr;
    }

    /**
     * 生产一个控制器对象
     *
     * @return Controller
     */
    public function create($name)
    {
        $className = 'Lxh\\' . $this->module . '\\Controller\\';
        if ($this->folder) {
            $className .= $this->folder . '\\';
        }
        $className .= $name;

        if (class_exists($className)) {
            $instance = new $className();
            $instance->setContainer($this->container);
            $instance->setControllerName($name);
            $instance->setManager($this);

            return $instance;
        }

        throw new NotFound("Controller '$className' not exists.", false);
    }

    /**
     * 获取中间件
     *
     * @return array
     */
    protected function getContrMiddleware(array & $middleware, Controller $instance)
    {
        $method = strtolower($this->actionName);

        foreach ($instance->getMiddleware() as $name => & $options) {
            if ($this->methodExcluded($method, $options)) {
                continue;
            }
            $middleware[] = $name;
        }
    }

    /**
     * 判断action方法是否被排除
     *
     * @return bool
     */
    public function methodExcluded(& $method, array & $options)
    {
        return (isset($options['only']) && ! in_array($method, $this->getMidMethods($options['only']))) ||
        (! empty($options['except']) && in_array($method, $this->getMidMethods($options['except'])));
    }

    // 把方法名转化为小写
    protected function getMidMethods(& $data)
    {
        foreach ((array) $data as & $method) {
            $method = strtolower($method);
        }
        return $data;
    }

    /**
     * 获取登录验证类实例
     * */
    public function getAuth()
    {
        if (! $this->auth) {
            $class = & $this->authParams;
            $className = '\\Lxh\\' . $this->module . '\\Auth\\' . $class;

            $this->auth = new $className($this->container, $this->request, $this->response);

            $this->container->instance('auth', $this->auth);
        }
        return $this->auth;
    }

    /**
     * 获取接口验证相关配置参数
     */
    protected function setAuthParams(& $params)
    {
//        var_dump($params);die;
        if ($params === false || $params) {
            $this->authParams = $params;
        }
    }

    public function controllerName()
    {
        return $this->controllerName ?: $this->defaultController;
    }

    public function actionName()
    {
        return $this->actionName;
    }

    public function moduleName()
    {
        return $this->module ?: $this->getDefaultModule();
    }

    public function getRequestParams()
    {
        return $this->requestParams;
    }

    protected function setModule($name)
    {
        $this->module = $name ?: $this->getDefaultModule();

        define('__MODULE__', $this->module);
    }

    protected function getDefaultModule()
    {
        $modules = config('modules');
        if (count($modules) < 1) {
            throw new RuntimeException('Get the default module failed');
        }
        return $modules[0];
    }

    protected function setFolder($name)
    {
        $this->folder = $name;
    }

    protected function setControllerName($name)
    {
        $this->controllerName = $name ? ucfirst($name) : $this->defaultController;

        language()->loadPackage('Global');
        language()->scope($this->controllerName);

        if (! defined('__CONTROLLER__')) {
            define('__CONTROLLER__', $this->controllerName);
        }
    }

    protected function setActionName($name)
    {
        $this->actionName = $name ?: $this->defaultAction;

        if (! defined('__ACTION__')) {
            define('__ACTION__', $this->actionName);
        }
    }

    protected function setRequestParams($params)
    {
        $this->requestParams = & $params;
    }

}
