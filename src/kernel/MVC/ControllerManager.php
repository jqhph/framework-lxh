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
use Lxh\Filters\Filter;
use Lxh\Helper\Util;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Pipeline;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\MVC\Controller;
use Symfony\Component\Console\Exception\RuntimeException;
use Lxh\Events\Dispatcher as Events;

class ControllerManager extends Factory
{

    /**
     * @var string
     */
    protected $defaultController = 'Index';// 默认控制器

    /**
     * @var string
     */
    protected $defaultAction = 'List';// 默认方法

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * 指定的身份鉴权类
     *
     * @var array
     */
    protected $authClasses = [

    ];

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $folder;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
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
     * @var string
     */
    protected $currentSettingMiddleware;

    /**
     * 额外添加的控制器中间件
     *
     * @var array
     */
    protected $controllersMiddlewares = [];

    /**
     * 用户登录验证类，默认开启验证
     * false表示不验证
     *
     * @var string | bool
     */
    protected $authParams = true;// 登录验证相关信息

    /**
     * @var string
     */
    protected $defaultAuthClass = 'User';

    /**
     * @var array
     */
    protected $requestParams = [];// 请求参数

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

    /**
     * @var Filter
     */
    protected $filters;

    public function __construct(
        Container $container, Request $request, Response $response, Pipeline $pipeline, Dispatcher $events, Filter $filter
    )
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        $this->pipeline = $pipeline;
        $this->events = $events;
        $this->filters = $filter;
    }

    /**
     * 控制器调度
     *
     * @return void
     */
    public function handle(Router $router)
    {
        $this->setModule($router->module, $router);
        $this->setFolder($router->folder, $router);
        $this->setControllerName($router->controllerName, $router);
        $this->setActionName($router->actionName, $router);
        $this->setRequestParams($router->requestParams);
        $this->setAuthParams($router->auth, $router);

        // 初始化语言包
        if (config('use-language')) {
            translator()->scope($this->controllerName);
        }

        if (! defined('__CONTROLLER__')) {
            define('__CONTROLLER__', $this->controllerName);
            define('__CONTROLLER_DASH__', lc_dash($this->controllerName));
        }

        if (! defined('__ACTION__')) {
            define('__ACTION__', $this->actionName);
        }

        if (! defined('__MODULE__')) {
            define('__MODULE__', $this->module);
            define('__MODULE_DASH__', lc_dash($this->module));
        }

        $this->response->append(
            $this->call($this->controllerName, $this->actionName, $this->requestParams)
        );

        $this->first = false;
    }

    /**
     * 设置身份鉴权类
     *
     * @param string $module
     * @param string $class
     * @return $this
     */
    public function setAuthClass($module, $class)
    {
        $this->authClasses[$module] = &$class;

        return $this;
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

        $action = 'action' . $action;

        // 检测action是否存在
        if (! method_exists($contr, $action)) {
            throw new NotFound("Call to undefined method " . get_class($contr) . "::{$action}().", false);
        }

        // 分派控制器前中间件
        $middleware = [];

        if ($this->first) {
            // 添加公共中间件
            $this->addMiddleware($middleware);
        }

        // 添加控制器中间件
        $this->getContrMiddleware($middleware, $contr);

        return $this->pipeline
            ->send([])
            ->through($middleware)
            ->then(function ($middlewareParams) use ($contr, $action, $params) {
                if ($this->first) {
                    // 存储中间件传递的参数
                    $this->request->setMiddlewaresParams($middlewareParams);

                    $this->events->fire(EVENT_AUTH_SUCCESS);

                    // 注册当前控制器
                    $this->container->instance('controller', $contr);
                }

                return $contr->$action($params);
            });
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->controllerClass;
    }

    /**
     * 添加中间件
     *
     * @param array $middleware
     */
    protected function addMiddleware(array &$middleware)
    {
        // 优先执行公共中间件
        foreach ((array) config('middlewares') as $module => &$mid) {
            if ($module == '*' || $module == $this->module) {
                $middleware = array_merge($middleware, (array) $mid);
            }
        }

        if ($this->authParams) {
            $middleware[] = $this->getAuth();
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
     * @param string $name
     * @return Controller
     */
    public function create($name)
    {
        if ($this->controllerClass) {
            $className = &$this->controllerClass;
        } else {
            $className = $this->getClassName($name);
        }

        // 保存当前控制器类名
        $this->controllerClass = trim($className, '//');

//        if (class_exists($className)) {
        return new $className($name, $this->container, $this);
//        }

//        throw new NotFound("Controller '$className' not exists.", false);
    }

    /**
     * @param $name
     * @param null $namespace
     * @return string
     */
    protected function getClassName(&$name, &$namespace = null)
    {
        $className = $namespace ?: 'Lxh\\' . $this->module . '\\Controllers\\';
        if ($this->folder) {
            $className .= $this->folder . '\\';
            $this->folder = null;
        }
        if (get_value($this->requestParams, 'api')) {
            $apiClass = "{$className}Api\\$name";
            if (class_exists($apiClass)) {
                $className = &$apiClass;
            } else {
                $className .= $name;
            }
        } else {
            $className .= $name;
        }
        return $className;
    }

    /**
     * 获取中间件
     *
     * @return void
     */
    protected function getContrMiddleware(array &$middleware, Controller $instance)
    {
        $method = strtolower($this->actionName);

        // 优先执行控制器自身添加的中间件
        foreach ($instance->getMiddleware() as $name => &$options) {
            if ($this->methodExcluded($method, $options)) {
                continue;
            }
            $middleware[] = $name;
        }

        if (!isset($this->controllersMiddlewares[$this->controllerClass])) {
            return;
        }

        foreach ($this->controllersMiddlewares[$this->controllerClass] as $name => &$options) {
            if ($this->methodExcluded($method, $options)) {
                continue;
            }
            $middleware[] = $name;
        }
    }

    /**
     * 添加控制器中间件
     *
     * @param string $controllerClass 控制器完整类名
     * @param string $middleware 类名@方法名 或 类名 或 容器注册的服务名
     * @return \Lxh\MVC\ControllerManager
     */
    public function addControllerMiddleware($controllerClass, $middleware)
    {
        // 保存当前设置的中间件
        $this->currentSettingMiddleware = [$controllerClass, $middleware];
        // 保存中间件
        $this->controllersMiddlewares[$controllerClass][$middleware] = [];

        return $this;
    }

    /**
     * 只有触发$methods方法中间件才会执行
     *
     * @param string|array $methods 多个请传数组
     * @return void
     */
    public function only($methods)
    {
        if (! $this->currentSettingMiddleware) {
            return;
        }

        list($c, $m) = $this->currentSettingMiddleware;
        $this->controllersMiddlewares[$c][$m]['only'] = $methods;
    }

    /**
     * 除了$methods方法外所有其他action方法都会中心中间件
     *
     * @param $methods string|array $methods 多个请传数组
     * @return void
     */
    public function except($methods)
    {
        if (! $this->currentSettingMiddleware) {
            return;
        }

        list($c, $m) = $this->currentSettingMiddleware;
        $this->controllersMiddlewares[$c][$m]['except'] = $methods;
    }

    /**
     * 判断action方法是否被排除
     *
     * @return bool
     */
    protected function methodExcluded(& $method, array & $options)
    {
        return (isset($options['only']) && ! in_array($method, $this->normalizeMiddlewareMethods($options['only']))) ||
        (! empty($options['except']) && in_array($method, $this->normalizeMiddlewareMethods($options['except'])));
    }

    /**
     * 把方法名转化为小写
     *
     * @param $data
     * @return array
     */
    protected function normalizeMiddlewareMethods(& $data)
    {
        $data = (array) $data;
        foreach ($data as & $method) {
            $method = strtolower($method);
        }
        return $data;
    }

    /**
     * 获取身份鉴权类实例
     * 必须身份鉴权类必须实现handle方法
     *
     * @return object
     */
    public function getAuth()
    {
        if (! $this->auth) {
            if (isset($this->authClasses[__MODULE__])) {
                $className = $this->authClasses[__MODULE__];
            } else {
                $className = strpos($this->authParams, '\\') !== false ? $this->authParams :
                    (is_bool($this->authParams) ? "\\Lxh\\{$this->module}\\Auth\\{$this->defaultAuthClass}"
                        : "\\Lxh\\{$this->module}\\Auth\\{$this->authParams}");
            }

            $this->auth = new $className($this->container, $this->request, $this->response);

            $this->container->instance('auth', $this->auth);
        }
        return $this->auth;
    }

    /**
     * 设置接口验证相关配置
     *
     * @param $params
     */
    protected function setAuthParams(& $params)
    {
//        var_dump($params);die;
        if ($params === false || $params) {
            $this->authParams = $this->filters->apply(
                'auth', $params
            );
        }
    }

    /**
     * 获取控制器名称
     *
     * @return string
     */
    public function controllerName()
    {
        return $this->controllerName ?: $this->defaultController;
    }

    public function actionName()
    {
        return $this->actionName;
    }

    /**
     * 获取模块名称
     *
     * @return mixed
     */
    public function moduleName()
    {
        return $this->module ?: $this->getDefaultModule();
    }

    /**
     * 获取小写下划线格式模块名
     *
     * @return string
     */
    public function moduleDash()
    {
        return defined('__MODULE_DASH__') ? __MODULE_DASH__ : lc_dash($this->getDefaultModule());
    }

    public function getRequestParams()
    {
        return $this->requestParams;
    }

    protected function setModule($name, $router)
    {
        $this->module = $this->filters->apply(
            'module', $name ?: $this->getDefaultModule(), $router
        );
    }

    /**
     * 获取默认模块
     *
     * @return mixed
     */
    protected function getDefaultModule()
    {
        $modules = (array) config('modules');
        if (count($modules) < 1) {
            throw new RuntimeException('Module params miss.');
        }
        return $modules[0];
    }

    /**
     * 设置控制器文件夹
     *
     * @param $name
     */
    protected function setFolder($name, $router)
    {
        $this->folder = $name;
    }

    /**
     * 设置控制器名称
     * 并由小写中划线格式转化为大驼峰格式
     *
     * @param $name
     */
    protected function setControllerName($name, $router)
    {
        $name = $this->filters->apply('setting.controller', Util::toCamelCase($name, true, '-'), $router);

        if (strpos($name, '\\') !== false) {
            // 传递的是完整类名
            $this->controllerClass = $name;

            $name = explode('\\', $name);
            $name = end($name);
        }
        $this->controllerName = $name;
    }

    /**
     * 设置action名称
     * 并由小写中划线格式转化为大驼峰格式
     *
     * @param $name
     */
    protected function setActionName($name, $router)
    {
        $this->actionName = $this->filters->apply(
            'setting.action',
            $name ? Util::toCamelCase($name, true, '-') : $this->defaultAction,
            $router
        );
    }

    protected function setRequestParams($params)
    {
        $this->requestParams = &$params;
        // 存储路由参数
        $this->request->setParams($params);
    }

}
