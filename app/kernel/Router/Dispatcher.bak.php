<?php
/**
 * 路由管理
 *
 * @author Jqh
 * @date   2017/5/22 10:47
 */

namespace Lxh\Router;

use Lxh\Contracts\Router;

class DispatcherBak implements Router
{
    protected $container;

    /**
     * 路由规则配置数组
     *
     * @var array
     */
    protected $config = [];

    /**
     * $_SERVER['REQUEST_URI']，去除“?”以及后面的参数
     *
     * @var string
     */
    protected $requestUri;

    /**
     * uri是否含有“-”，有则需要用正则路由规则匹配
     *
     * @var bool
     */
    protected $isRegUri;

    /**
     * 任意定界符
     *
     * @var string
     */
    protected $signOfAny = ':';

    /**
     * 正则符号
     *
     * @var string
     */
    protected $regularSymbol = '@';

    /**
     * uri正则匹配符号
     *
     * @var string
     */
    protected $regularSymbolByUri = '-';

    /**
     * 路由钩子
     */
    protected $hooks = [];

    /**
     * 路由解析结果
     *
     * @var string
     */
    protected $matchResult;

    /**
     * uri数组
     *
     * @var array
     */
    protected $uriarr;

    /**
     * 控制器名称
     *
     * @var string
     */
    public $controllerName;

    /**
     * action名称
     *
     * @var string
     */
    public $actionName;

    /**
     * 安全验证配置信息
     *
     * @var array
     */
    public $auth;

    /**
     * 请求参数
     *
     * @var array
     */
    public $requestParams;

    /**
     * 控制器文件夹
     *
     * @var string
     */
    public $folder;

    /**
     * 路由匹配规则前缀
     *
     * @var string
     */
    protected $routePrefix;

    /**
     * 模块名称
     *
     * @var string
     */
    public $module;

    /**
     * 正则匹配成功后的结果数据
     *
     * @var array
     */
    protected $regResultData = [];

    public function __construct($container = null, array $config = [])
    {
        $this->container = null;

        $this->config = $config;

        $this->routePrefix = config('route-prefix');
    }

    // 添加路由规则配置
    public function add(array $config)
    {
        $this->config[] = $config;
    }

    // 设置路由规则配置
    public function fill(array $config)
    {
        $this->config = $config;
    }

    public function handle()
    {
        $this->folder         = '';
        $this->controllerName = I('c');
        $this->actionName	  = I('a');
        $this->auth		      = [];
        $this->requestParams  = [];

        // 解析路由
        $this->matchResult = self::SUCCESS;
    }


    /**
     * 获取路由匹配结果
     *
     * @param
     * @return mixed
     */
    public function getDispatchResult()
    {
        return $this->matchResult;
    }

}
