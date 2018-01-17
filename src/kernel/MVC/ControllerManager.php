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

    public function __construct(
        Container $container, Request $request, Response $response, Pipeline $pipeline, Dispatcher $events
    )
    {
        parent::__construct($container);

        $this->request = $request;
        $this->response = $response;
        $this->pipeline = $pipeline;
        $this->events = $events;
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

        // 初始化语言包
        if (config('use-language')) {
            $language = language();
            $language->loadPackage('Global');
            $language->scope($this->controllerName);
        }

        if (! defined('__CONTROLLER__')) {
            define('__CONTROLLER__', $this->controllerName);
        }

        if (! defined('__ACTION__')) {
            define('__ACTION__', $this->actionName);
        }

        if (! defined('__MODULE__')) {
            define('__MODULE__', $this->module);
        }

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
        // 优先执行公共中间件
        foreach ((array) config('middlewares') as $module => & $mid) {
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
     * @return Controller
     */
    public function create($name)
    {
        $className = 'Lxh\\' . $this->module . '\\Controllers\\';
        if ($this->folder) {
            $className .= $this->folder . '\\';

            $this->folder = null;
        }
        if (get_value($this->requestParams, 'api')) {
            $apiClass = $className . "Api\\$name";
            if (class_exists($apiClass)) {
                $className = $apiClass;
            } else {
                $className .= $name;
            }
        } else {
            $className .= $name;
        }

        if (class_exists($className)) {
            $instance = new $className($name, $this->container, $this);

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
        $data = (array) $data;
        foreach ($data as & $method) {
            $method = strtolower($method);
        }
        return $data;
    }

    /**
     * 获取身份鉴权类实例
     *
     */
    public function getAuth()
    {
        if (! $this->auth) {
            $className = strpos($this->authParams, '\\') !== false ? $this->authParams :
                (is_bool($this->authParams) ? "\\Lxh\\{$this->module}\\Auth\\{$this->defaultAuthClass}" : "\\Lxh\\{$this->module}\\Auth\\{$this->authParams}");

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
    }

    protected function getDefaultModule()
    {
        $modules = (array) config('modules');
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
        $this->controllerName = $name ? Util::toCamelCase($name, true, '-') : $this->defaultController;
    }

    protected function setActionName($name)
    {
        $this->actionName = $name ? Util::toCamelCase($name, true, '-') : $this->defaultAction;
    }

    protected function setRequestParams($params)
    {
        $this->requestParams = &$params;
        // 存储路由参数
        $this->request->setParams($params);
    }

}
