<?php
/**
 * 应用程序管理
 *
 * @author Jqh
 * @date   2017/6/13 18:05
 */
namespace Lxh;

use Lxh\Exceptions\Exception;
use Lxh\Exceptions\NotFound;
use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\Helper\Console;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Plugins\Plugin;
use Lxh\Router\Dispatcher as Router;
use Lxh\View\ViewServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Lxh\Crontab\Application as Crontab;

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
    /**
     * @var array
     */
    protected $commands = [];
    /**
     * Application constructor.
     * @param string $rootDir 项目根目录
     */
    public function __construct()
    {
        ob_start();
        $this->define();
        $this->loadInitConfig();
        $this->loadFunctionFile();
        $this->loadServiceBindConfig();

        $this->events    = events();
        $this->container = container();
        $this->response = response();
        $this->request = request();
        $this->container->instance('app', $this);
        $this->bindRouter();

        // 设置时区
        if ($timezone = config('timezone')) {
            date_default_timezone_set($timezone);
        }
        register_shutdown_function([$this, 'shutdown']);
        // 记录程序执行开始时间
        debug_track('start');

    }

    /**
     * 定义系统常量
     */
    protected function define()
    {
        require __DIR__ . '/define.php';
        $this->root = __ROOT__;
    }

    /**
     * 获取web目录路径
     *
     * @return string
     */
    public function getPublicPath()
    {
        if (defined('__PUBLIC_ROOT__')) {
            return __PUBLIC_ROOT__;
        }
        return dirname($this->root) . '/public/';
    }
    /**
     * 获取data目录路径
     *
     * @return string
     */
    public function getDataPath()
    {
        return __DATA_ROOT__;
    }
    /**
     * 程序异常终结
     *
     * @return void
     */
    public function shutdown()
    {
        // 触发程序终结事件
//        $this->events->fire('app.shutdown');
        $this->response->send();
    }

    /**
     * 注册插件
     */
    protected function registerPlugins($router)
    {
        foreach ((array)config('plugins') as $name => &$namespace) {
            Plugin::createApplication($namespace)->register();
        }

        foreach ((array)config('providers') as &$provider) {
            $provider = (new $provider($router))->register();
        }
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
        $this->events->listen(EVENT_EXCEPTION, 'exception.handler');
    }

    /**
     * 运行WEB应用
     *
     * @return Response
     */
    public function handle()
    {
        try {
            // 添加事件监听
            $this->addListeners();
            // 触发路由调度前事件
            $this->events->fire(EVENT_ROUTE_DISPATCH_BEFORE);
            $router = $this->container['router'];

            // 开始路由调度
            if (! $router->handle()) {
                throw new NotFound();
            }
            // 触发路由调度成功后事件
            $this->events->fire(EVENT_ROUTE_DISPATCH_AFTER);

            $this->container['controller.manager']->handle($router);

            return $this->response;
        } catch (\Exception $e) {
            $this->events->fire(EVENT_EXCEPTION, [$e], true);
            return $this->response;
        }
    }
    /**
     * 载入公共函数文件
     *
     * @return void
     */
    protected function loadFunctionFile()
    {
        require $this->root . 'kernel/Helper/func.php';
        require __APP__ . 'Kernel/Support/func.php';
    }

    /**
     * 载入初始化配置文件
     *
     * @return void
     */
    protected function loadInitConfig()
    {
        require __CONFIG__ . 'ini.php';
        // 如果没有定义当前环境常量，则默认为测试环境
        if (! defined('__ENV__')) {
            define('__ENV__', ENV_DEV);
        }
        require __CONFIG__ . __ENV__ . '/ini.php';

        if (! defined('__DATA_ROOT__')) {
            define('__DATA_ROOT__', dirname($this->root) . '/data/');
        }
    }

    /**
     * 加载容器配置文件
     */
    protected function loadServiceBindConfig()
    {
        require __CONFIG__ . 'container/bind.php';
    }

    /**
     * 注册路由服务
     *
     * @return void
     */
    protected function bindRouter()
    {
        $router = new Router();
        // 先注册路由
        $this->container->instance('router', $router);

        // 再注册插件
        $this->registerPlugins($router);

        // 最后注册路由规则
        $router->attach($this->getRouteRules());
    }

    /**
     * @return array
     */
    protected function getRouteRules()
    {
        $routers = [];
        if (config('admin.use-admin-routes', true)) {
            $routers = include __DIR__ . '/Router/admin-routes.php';
        }

        $configPath = __CONFIG__ . 'route/route.php';
        // 判断是否开启了子域名部署
        if (config('domain-deploy')) {
            $domains = config('domain-deploy-config');
            $host = $this->request->host();
            if (substr_count($host, '.') < 2) {
                $host = 'www.' . $host;
            }
            $module = get_value($domains, $host);
            $path = __CONFIG__ . "route/{$module}.php";
            if (is_file($path)) {
                $configPath = & $path;
            }
        }
        return array_merge($routers, (array) include $configPath);
    }

    /**
     * @param array $commands
     * @return $this
     */
    public function addCommands(array $commands)
    {
        $this->commands = array_merge($this->commands, $commands);
        return $this;
    }
    /**
     * 执行命令
     *
     * @return void
     */
    public function console()
    {
        define('CONSOLE_START', microtime(true));
        $this->container['console']->handle();
    }
    /**
     * 定时任务
     *
     */
    public function crontab()
    {
        $this->container['crontab']->handle();
    }
}
