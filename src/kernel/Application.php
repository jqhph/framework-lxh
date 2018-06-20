<?php

namespace Lxh;

use Lxh\Exceptions\NotFound;
use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Plugins\Plugin;
use Lxh\Router\Dispatcher as Router;

class Application
{
    /**
     * @var Container
     */
    public $container;

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
    public $events;

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * 别名
     *
     * @var array
     */
    private static $aliases = [];

    /**
     * Application constructor.
     */
    public function __construct()
    {
        ob_start();

        $this->setup();
        $this->init();

        static::setAlias('@lxh', dirname(__DIR__));
    }

    /**
     * 版本号
     *
     * @return string
     */
    public static function version()
    {
        return '1.0.0-dev';
    }

    protected function setup()
    {
        $this->define();
        $this->includeConfigs();
        $this->includeHelpers();

        $this->events    = events();
        $this->container = container();
        $this->response  = response();
        $this->request   = request();
        $this->container->instance('app', $this);

        $this->setupRouter();
    }

    protected function init()
    {
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
    }

    /**
     * 注册多个别名
     *
     * @param array $aliases 别名数组
     *                       <pre>
     *                       [
     *                       '@root' => BASE_PATH
     *                       ......
     *                       ]
     *                       </pre>
     *
     * @throws \InvalidArgumentException
     */
    public static function setAliases(array $aliases)
    {
        foreach ($aliases as $name => $path) {
            self::setAlias($name, $path);
        }
    }

    /**
     * Set alias
     *
     * @param string $alias alias
     * @param string $path  path
     *
     * @throws \InvalidArgumentException
     */
    public static function setAlias($alias, $path = null)
    {
        if ($alias[0] !== '@') {
            $alias = '@' . $alias;
        }

        // Delete alias
        if (!$path) {
            unset(self::$aliases[$alias]);

            return;
        }

        // $path 不是别名，直接设置
        if ($path[0] !== '@') {
            self::$aliases[$alias] = $path;

            return;
        }

        // $path是一个别名
        if (isset(self::$aliases[$path])) {
            self::$aliases[$alias] = self::$aliases[$path];

            return;
        }

        list($root) = explode('/', $path);
        if (!isset(self::$aliases[$root])) {
            throw new \InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = str_replace($root, '', $path);

        self::$aliases[$alias] = $rootPath . $aliasPath;
    }

    /**
     * Get alias
     *
     * @param string $alias
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getAlias($alias)
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return $alias;
        }

        if (isset(self::$aliases[$alias])) {
            return self::$aliases[$alias];
        }

        list($root) = \explode('/', $alias, 2);
        if (!isset(self::$aliases[$root])) {
            throw new \InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = \str_replace($root, '', $alias);

        return $rootPath . $aliasPath;
    }

    /**
     * Is alias exist ?
     *
     * @param string $alias
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function hasAlias($alias)
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return false;
        }

        return isset(self::$aliases[$alias]);
    }

    /**
     * 获取web目录路径
     *
     * @return string
     */
    public static function getPublicPath()
    {
        return dirname(__ROOT__) . '/public/';
    }

    /**
     * 获取data目录路径
     *
     * @return string
     */
    public static function getDataPath()
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
    protected function includeHelpers()
    {
        require __ROOT__ . 'kernel/Support/helpers.php';

        $path = __APP__ . 'Support/helpers.php';
        if (is_file($path)) {
            require $path;
        }
    }

    /**
     * 载入初始化配置文件
     *
     * @return void
     */
    protected function includeConfigs()
    {
        require __CONFIG__ . 'ini.php';
        // 如果没有定义当前环境常量，则默认为测试环境
        if (! defined('__ENV__')) {
            define('__ENV__', ENV_DEV);
        }
    }

    /**
     * 注册路由服务
     *
     * @return void
     */
    protected function setupRouter()
    {
        $router = new Router();

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

        if (config('home.use-routes', true)) {
            $routers = include __DIR__ . '/Home/resource/routes.php';
        }

        if (config('admin.use-routes', true)) {
            $routers = array_merge($routers, include __DIR__ . '/Admin/resource/routes.php');
        }

        $configPath = __CONFIG__ . 'routes/route.php';

        // 判断是否开启了子域名部署
        if ($domains = config('domain-deploy-config')) {
            $host = $this->request->host();

            $module = getvalue($domains, $host);

            $path = __CONFIG__ . "routes/{$module}.php";
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
