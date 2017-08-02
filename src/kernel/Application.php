<?php
/**
 * 应用程序管理
 *
 * @author Jqh
 * @date   2017/6/13 18:05
 */

namespace Lxh;

use Lxh\Exceptions\Exception;
use Lxh\Contracts\Router;
use Lxh\Exceptions\NotFound;
use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\Http\Response;
use Lxh\Http\Request;

class Application
{
    /**
     * 根目录
     *
     * @var string
     */
    protected $root;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct($rootDir)
    {
        define('ROOT_PATH', $rootDir . '/');
        define('__ROOT__', ROOT_PATH);

        $this->root = __ROOT__;

        $this->loadInitConfig();
        $this->loadFunctionFile();
        $this->loadServiceBindConfig();

        // 记录程序执行开始时间
        debug_track('start');

        $this->container = container();
        $this->events    = events();
    }

    /**
     * 添加事件监听者
     *
     * @return void
     */
    protected function addListeners()
    {
        foreach (config('events') as $event => & $listeners) {
            foreach ((array) $listeners as & $listener) {
                $this->events->listen($event, $listener);
            }
        }

        $this->events->listen('exception', 'exception.handler');
    }

    /**
     * 运行WEB应用
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->response = $this->container->make('http.response');

            $this->request = $this->container->make('http.request');

            // 添加事件监听
            $this->addListeners();

            // 触发路由匹配前置事件
            $this->events->fire('route.dispatch.before', [$this->request, $this->response]);

            $router = $this->container->make('router');

            $router->handle();

            switch ($router->getDispatchResult()) {
                case Router::SUCCESS:
                    $this->container->make('controller.manager')->handle($router, $this->request, $this->response);
                    break;
                default:
                    throw new NotFound();
            }

            return $this->response;
        } catch (\Exception $e) {
            $this->events->fire('exception', [$e]);
        }
    }

    /**
     * 载入公共函数文件
     *
     * @return void
     */
    protected function loadFunctionFile()
    {
        require $this->root . 'Kernel/Helper/func.php';
        require $this->root . 'application/Kernel/Support/func.php';
    }

    /**
     * 载入初始化配置文件
     *
     * @return void
     */
    protected function loadInitConfig()
    {
        require $this->root . 'config/ini.php';
    }

    protected function loadServiceBindConfig()
    {
        require $this->root . 'config/container/bind.php';
    }

    /**
     * 执行命令台命令
     *
     * @return void
     */
    public function console()
    {
        define('CONSOLE_START', microtime(true));

        $this->container->make('console')->handle();

    }
}
