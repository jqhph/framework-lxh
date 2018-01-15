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
        $this->container->instance('app', $this);
        if ($timezone = config('timezone')) {
            date_default_timezone_set($timezone);
        }
        register_shutdown_function([$this, 'shutdown']);
        // 记录程序执行开始时间
        debug_track('start');
        $this->bindRouter();
    }
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
        if (defined('__DATA_ROOT__')) {
            return __DATA_ROOT__;
        }
        return dirname($this->root) . '/data/';
    }
    /**
     * 程序异常终结
     *
     * @return void
     */
    public function shutdown()
    {
        // 触发程序终结事件
        $this->events->fire('app.shutdown', [$this->response]);
        $this->container['http.response']->send();
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
            $this->response = $this->container['http.response'];
            $this->request = $this->container['http.request'];
            // 添加事件监听
            $this->addListeners();
            // 触发路由匹配前置事件
            $this->events->fire(EVENT_ROUTE_DISPATCH_BEFORE);
            $router = $this->container['router'];
            if (! $router->handle()) {
                throw new NotFound();
            }
            // 触发陆游匹配成功后事件
            $this->events->fire(EVENT_ROUTE_DISPATCH_AFTER, [$router->requestParams]);
            $this->container['controller.manager']->handle($router);
            return $this->response;
        } catch (\Exception $e) {
            $this->events->fire('exception', [$e]);
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
    }
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
        $this->container->singleton('router', function (Container $container) {
            $request = $container['http.request'];
            $configPath = __CONFIG__ . 'route/route.php';
            // 判断是否开启了子域名部署
            if (config('domain-deploy')) {
                $domains = config('domain-deploy-config');
                $host = $request->host();
                if (substr_count($host, '.') < 2) {
                    $host = 'www.' . $host;
                }
                $module = get_value($domains, $host);
                $path = __CONFIG__ . "route/{$module}.php";
                if (is_file($path)) {
                    $configPath = & $path;
                }
            }
            return new Router((array) include $configPath);
        });
    }
    public function addCommands(array $commands)
    {
        $this->commands = array_merge($this->commands, $commands);
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
