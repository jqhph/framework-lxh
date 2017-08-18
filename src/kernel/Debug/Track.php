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
    // 项目开始执行时间
    protected $startTime;

    // 项目总运行时间
    protected $runTime;

    /**
     * @var Container
     */
    protected $container;

    protected $records = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    // 设置程序运行时间
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
                $store = $this->getRecordStore($name);
                if (empty($options['command'])) {
                    return;
                }

                $store->command($options['command']);
                $store->time(get_value($options, 'start'));
                $store->type(get_value($options, 'type'));
                $store->params(get_value($options, 'params', []));
        }
    }

    // 输出调试信息权限检测
    protected function checkResponseAccess(Request $request)
    {
        // 生产环境，不记录任何信息
        return (! is_prod() && config('response-trace-log', true) == true && ! $request->isCli()) ? true : false;
    }

    /**
     * 获取追踪记录存储库
     *
     * @param $name
     * @return Record
     */
    protected function getRecordStore($name)
    {
        if (isset($this->records[$name])) {
            return $this->records[$name];
        }
        if ($name == 'db') {
            return $this->records[$name] = new Database();
        }
        return $this->records[$name] = new Record();
    }

    /**
     * 处理记录追踪信息接口
     *
     * @param Request $request
     * @param Response $response
     */
    public function handle(Request $request, Response $response)
    {
        if ($this->checkResponseAccess($request)) {
            $this->response($request);
        }

    }

    /**
     * 输出追踪信息
     */
    protected function response(Request $request)
    {
        $db = $this->getRecordStore('db');
        $controllerManager = $this->container->make('controller.manager');

        $requestInfo = ' [Module: ' . $controllerManager->moduleName() . ', Controller: ' . $controllerManager->controllerName()
                        . ', Action: ' . $controllerManager->actionName() . '] '
                        . $request->date() . ' ' . $request->protocol(). ' ' . $request->getMethod() . ': '
                        . $request->getUri()->getPath() . ' ' . $request->getUri()->getQuery();

        $base = [
            '请求信息'     => & $requestInfo,
            '运行时间'     => $this->getRunTime(),
//            '吞吐率'	    => number_format(1 / $this->getRunTime(), 2) . 'req/s',
            '内存开销'     => number_format((memory_get_usage()) / 1024, 2) . 'kb',
            '最后执行SQL' 	=> $db->last(),
            '查询信息'       => $db->computeTypeTimes('r') . ' queries ' . $db->computeTypeTimes('w') . ' writes ' . $db->computeTypeTimes('c') . ' connected',
            '数据库操作详情' => $db->all(),
            '自定义追踪'     => $this->getAllRecords(),
            '缓存信息'       => ' gets ' . ' writes ' . ' connected',
            '文件加载数量'   => count(get_included_files()),
            '文件加载详情'   => get_included_files(),
            '会话信息'       => 'SESSION_ID='.session_id(),
            'SERVER'         => & $_SERVER
        ];

        Console::info('%c[Trace Information]', 'color:chocolate;font-weight:bold', $base);

    }

    protected function getAllRecords()
    {
        $records = [];

        foreach ($this->records as $k => & $v) {
            if ($k == 'db') {
                continue;
            }
            $records[$k] = $v->full();
        }

        return $records;
    }

    // 保存性能统计信息
    protected function save(array $base)
    {

    }

    /**
     * 项目运行时间
     */
    public function getRunTime()
    {
        if (! $this->runTime) {
            $this->runTime = microtime(true) - $this->startTime;
        }
        return $this->runTime;
    }

}
