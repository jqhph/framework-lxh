<?php
/**
 * 性能追踪
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 10:38
 */

namespace Lxh\Debug;

use Lxh\Contracts\Container\Container;
use Lxh\Debug\Data\Database;
use Lxh\Debug\Data\Record;
use Lxh\Helper\Arr;
use Lxh\Helper\Console;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Helper\ChromePhp;

class Track
{
    /**
     * 项目开始执行时间
     *
     * @var float
     */
    protected $startTime;

    /**
     * 项目总运行时间
     *
     * @var float
     */
    protected $runTime;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $stores = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 设置程序运行时间
     *
     * @param $time
     */
    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    /**
     * 记录追踪信息
     *
     * @param string $name    名称
     * @param mixed  $options 记录具体内容， 基本格式
     *                  [
     *                      'command' => 'test',    // 要记录的命令，不可为空
     *                      'start' => microtime(true), // 开始时间
     *                      'type' => 'w',          // 类型，自定义，可为空
     *                      'params' => []          // 其余参数，自定义，可为空
     *                  ]
     * @param bool   $save    是否持久化存储
     * @return mixed
     */
    public function record($name, $options = '', $save = false)
    {
        switch ($name) {
            case 'start':
                $this->startTime = $options ? $options : microtime(true);
                break;
            default:
                $store = $this->store($name);
                if (empty($options['command'])) {
                    return;
                }

                $store->command($options['command']);
                $store->time(get_value($options, 'start'));
                $store->type(get_value($options, 'type'));
                $store->params(get_value($options, 'params', []));
        }
    }

    /**
     * 输出调试信息权限检测
     *
     * @param Request $request
     * @return bool
     */
    protected function checkResponseAccess(Request $request)
    {
        // 生产环境，不记录任何信息
        return (! is_prod() && config('response-trace-log', true) == true) ? true : false;
    }

    /**
     * 获取追踪记录存储库
     *
     * @param $name
     * @return Record
     */
    protected function store($name)
    {
        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }
        if ($name == 'db') {
            return $this->stores[$name] = new Database();
        }
        return $this->stores[$name] = new Record();
    }

    /**
     * 处理记录追踪信息接口
     *
     * @param Request $request
     * @param Response $response
     */
    public function handle()
    {
        $request = request();
        if ($this->checkResponseAccess($request)) {
            $this->response($request);
        }

    }

    /**
     * 输出追踪信息
     */
    protected function response(Request $request)
    {
        $db = $this->store('db');
        $controllerManager = $this->container->make('controller.manager');
        $uri = $request->getUri();

        $requestInfo = ' [Module: ' . $controllerManager->moduleName()
                        . ', Controller: ' . $controllerManager->getClass()
                        . ', Action: ' . $controllerManager->actionName() . '] '
                        . $request->date() . ' ' . $request->protocol()
                        . ' ' . $request->getMethod() . ': '
                        . $uri->getPath() . ' ' . $uri->getQuery();

        $allFiles = get_included_files();

        $session = $this->container['session'];
        $cookie = $this->container['cookie'];

        $base = [
            '请求信息'      => & $requestInfo,
            '运行时间'      => $this->usetime(),
//            '吞吐率'	    => number_format(1 / $this->getRunTime(), 2) . 'req/s',
            '内存开销'      => number_format((memory_get_usage()) / 1024, 2) . 'kb',
            '最后执行SQL' 	=> $db->last(),
            '数据库信息'     => $db->typesCount('r') . ' queries ' . $db->typesCount('w') . ' writes ' . $db->typesCount('c') . ' connected',
            '数据库操作详情' => $db->all(),
            '自定义追踪'     => $this->getCustomTrackInfos(),
            '缓存信息'       => ' gets ' . ' writes ' . ' connected',
            '文件加载数量'   => count($allFiles),
            '文件加载详情'   => & $allFiles,
            'SERVER'         => & $_SERVER,
            '配置参数'       => $this->container->config->toArray(),
            '路由配置'       => $this->container->router->rules(),
            'SESSION'        => ['items' => $session->toArray(), 'config' => $session->config()],
            'COOKIE'         => ['items' => $cookie->toArray(), 'config' => $cookie->config()],
        ];

        Console::info('%c[Trace Information]', 'color:chocolate;font-weight:bold', $base);

    }

    /**
     * @return array
     */
    protected function getCustomTrackInfos()
    {
        $records = [];
        foreach ($this->stores as $k => &$v) {
            if ($k == 'db') {
                continue;
            }
            $records[$k] = $v->computes();
        }

        return $records;
    }

    // 保存性能统计信息
    protected function save(array $base)
    {

    }

    /**
     * 项目运行时间
     *
     * @return float
     */
    public function usetime()
    {
        if (! $this->runTime) {
            $this->runTime = microtime(true) - $this->startTime;
        }
        return $this->runTime;
    }

}
