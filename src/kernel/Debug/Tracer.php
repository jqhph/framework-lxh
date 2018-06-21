<?php
/**
 * 追踪
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 10:38
 */

namespace Lxh\Debug;

use Lxh\Contracts\Container\Container;
use Lxh\Debug\Records\Database;
use Lxh\Helper\Console;
use Lxh\Http\Response;
use Lxh\Http\Request;

class Tracer
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * 性能日志
     *
     * @var array
     */
    protected $profiles = [];

    /**
     * 标记栈
     *
     * @var array
     */
    protected $profileStacks = [];

    /**
     * @var array
     */
    protected $countings = [];

    /**
     * 数据库标记栈
     *
     * @var array
     */
    protected $databaseStacks = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Database $database
     * @return $this
     */
    public function addDatabaseRecord(Database $database)
    {
        $this->databaseStacks[] = $database;

        return $this;
    }

    /**
     * 获取数据库追踪信息
     *
     * @return array
     */
    protected function getDatabaseInfo()
    {
        $data = [];
        foreach ($this->databaseStacks as $database) {
            $data[] = $database->toArray();
        }

        return $data;
    }

    /**
     * 标记开始
     *
     * @param string $name 标记名称
     * @param float $time
     */
    public function profileStart($name, $time = null)
    {
        if (is_string($name) === false || empty($name)) {
            return;
        }

        $this->profileStacks[$name]['start'] = $time ?: microtime(true);
    }

    /**
     * 标记开始
     *
     * @param string $name 标记名称
     */
    public function profileEnd($name)
    {
        if (is_string($name) === false || empty($name)) {
            return;
        }

        if (! isset($this->profiles[$name])) {
            $this->profiles[$name] = [
                'cost'  => 0,
                'total' => 0,
            ];
        }

        $this->profiles[$name]['cost'] += microtime(true) - $this->profileStacks[$name]['start'];
        $this->profiles[$name]['total'] += 1;
    }

    /**
     * 组装profiles
     *
     * @return string
     */
    public function getProfilesInfos()
    {
        $profileAry = [];
        foreach ($this->profiles as $key => $profile) {
            if (!isset($profile['cost'], $profile['total'])) {
                continue;
            }
            $cost = sprintf('%.2f', $profile['cost'] * 1000);
            $profileAry[] = "$key=" . $cost . '(ms)/' . $profile['total'];
        }

        return implode(',', $profileAry);
    }

    /**
     * 缓存命中率计算
     *
     * @param string $name  计算KEY
     * @param int    $hit   命中数
     * @param int    $total 总数
     */
    public function counting($name, $hit, $total = null)
    {
        if (! \is_string($name) || empty($name)) {
            return;
        }

        if (! isset($this->countings[$name])) {
            $this->countings[$name] = ['hit' => 0, 'total' => 0];
        }
        $this->countings[$name]['hit'] += (int)$hit;
        if ($total !== null) {
            $this->countings[$name]['total'] += (int)$total;
        }
    }

    /**
     * 组装字符串
     *
     * @return string
     */
    public function getCountingInfo()
    {
        if (! isset($this->countings) || empty($this->countings)) {
            return '';
        }

        $countAry = [];
        foreach ($this->countings as $name => $counter) {
            if (isset($counter['hit'], $counter['total']) && $counter['total'] !== 0) {
                $countAry[] = "$name=" . $counter['hit'] . '/' . $counter['total'];
            } elseif (isset($counter['hit'])) {
                $countAry[] = "$name=" . $counter['hit'];
            }
        }
        return implode(',', $countAry);
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
        return (! is_prod() && config('response-trace-log', true) == true && !is_cli()) ? true : false;
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
        $lastDatabase = end($this->databaseStacks);
        $controllerManager = $this->container->make('controllerManager');
        $uri = $request->getUri();

        $requestInfo = ' [Module: ' . $controllerManager->moduleName()
            . ', Controller: ' . $controllerManager->getClass()
            . ', Action: ' . $controllerManager->actionName() . '] '
            . $request->date() . ' ' . $request->protocol()
            . ' ' . $request->getMethod() . ': '
            . $uri->getPath() . ' ' . $uri->getQuery();

        $allFiles = get_included_files();

        $session = $this->container->session;
        $cookie = $this->container->cookie;

        $base = [
            '请求信息'      => &$requestInfo,
            '性能'          => $this->getProfilesInfos(),
//            '吞吐率'	    => number_format(1 / $this->getRunTime(), 2) . 'req/s',
            '内存开销'      => number_format((memory_get_usage()) / 1024, 2) . 'kb',
            '最后执行SQL' 	=> $lastDatabase ? $lastDatabase->toArray() : [],
//            '数据库信息'     => 0 . ' queries ' . 0 . ' writes ' . 0 . ' connected',
            '数据库操作详情' => $this->getDatabaseInfo(),
            '缓存信息'       => ' gets ' . ' writes ' . ' connected',
            '文件加载数量'   => count($allFiles),
            '文件加载详情'   => &$allFiles,
            'SERVER'         => &$_SERVER,
            '配置参数'       => $this->container->config->toArray(),
            '路由配置'       => $this->container->router->rules(),
            'SESSION'        => ['items' => $session->toArray(), 'config' => $session->config()],
            'COOKIE'         => ['items' => $cookie->toArray(), 'config' => $cookie->config()],
        ];

        Console::info('%c[Trace Information]', 'color:chocolate;font-weight:bold', $base);

    }

}
