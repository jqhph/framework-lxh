<?php
/**
 * 路由管理
 *
 * @author Jqh
 * @date   2017/5/22 10:47
 */

namespace Lxh\Router;

use Lxh\Contracts\Router;

class Dispatcher implements Router
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
//        $this->container->make('events')->fire('route.run.before');

        // 解析路由
        $this->matchResult = $this->dispatch();

        $this->afterDispatch();
    }

    /**
     * 匹配后置方法
     */
    protected function afterDispatch()
    {

    }

    /**
     * 解析路由
     *
     * @return string
     */
    protected function dispatch()
    {
        $uri = $this->getUri();

        $uriarr = explode('/', $uri);
        $this->arrayFilter($uriarr);

        $this->uriarr = & $uriarr;// 保存

        $ulen = count($uriarr);

        // 匹配路由
        foreach ($this->getRouterRules() as & $rule) {
            if ($this->matching($rule, $uriarr, $ulen)) {
                return self::SUCCESS;
            }
        }

        return self::NOTFOUND;
    }

    /**
     * 匹配路由
     *
     * @param $rule array 路由配置
     * @param $uriarr array uri数组
     * @param $ulen int uri数组长度
     * @return bool
     */
    protected function matching(array & $rule, array & $uriarr, $ulen)
    {
        if (empty($rule['pattern'])) {
            return false;
        }

        if (! isset($rule['auth'])) {
            $rule['auth'] = '';
        }

        // 匹配路由模式
        if (! $this->matchingPattern($rule, $uriarr, $ulen)) {
            return false;
        }

        // 匹配成功，缓存路由信息
        $this->save($rule, $uriarr);

        return true;
    }

    /**
     * 路由匹配成功，缓存相关数据
     *
     * @param array $rule
     * @param array $uriarr
     * @return void
     */
    protected function save(array & $rule, array & $uriarr)
    {
        // 验证配置
        $auth = & $rule['auth'];

        $contr = $action = '';

        $params = [];// 参数

        // 设置模块
        $this->setModule($rule);

        foreach (get_value($rule, 'params', []) as $k => & $p) {
            switch ($k) {
                case 'controller':
                    $contr = & $p;
                    break;
                case 'action':
                    $action = & $p;
                    break;
                default:
                    $params[$k] = & $p;
            }
        }

        $realContr = $realAction = '';
        foreach ($rule['pattern'] as $k => & $r) {
            if ($r == $contr) {
                $realContr = & $uriarr[$k];
            }

            if ($r == $action) {
                $realAction = & $uriarr[$k];
            }

            // 存在正则匹配
            if (isset($this->regResultData[$contr])) {
                $realContr = $this->regResultData[$contr];
            }
            if (isset($this->regResultData[$action])) {
                $realAction = $this->regResultData[$action];
            }

            foreach ($params as $pn => & $param) {
                if ($param == $r) {
                    $param = $uriarr[$k];
                }

                if (isset($this->regResultData[$param])) {
                    $param = $this->regResultData[$param];
                }
            }
        }

        if (! $realContr) {
            $realContr = & $contr;
        }
        if (! $realAction) {
            $realAction = & $action;
        }

        // 控制器文件夹
        $this->folder         = get_value($rule, 'folder');
        $this->hooks 		  = (array) get_value($rule, 'hooks', []);
        $this->controllerName = & $realContr;
        $this->actionName	  = & $realAction;
        $this->auth		      = & $auth;
        $this->requestParams  = & $params;
    }

    /**
     * 匹配路由模式
     *
     * @return bool
     */
    protected function matchingPattern(& $rule, & $uriarr, $ulen)
    {
        // 判断是否含有正则字符串
        $hasReg = $this->hasReg($rule['pattern']);

        // 路由模式
        $rule['pattern'] = explode('/', $rule['pattern']);

        // 添加配置文件制定的路由前缀
        if ($this->routePrefix) {
            array_unshift($rule['pattern'], $this->routePrefix);
        }

        $this->arrayFilter($rule['pattern']);

        if ($ulen != count($rule['pattern'])) {
            return false;
        }

        // 请求方法匹配，默认GET方法
        $method = get_value($rule, 'method', 'GET');
        if ($method != '*' && (strpos($method, $_SERVER['REQUEST_METHOD']) === false)) {
            return false;
        }

        if ($hasReg) {
            // 正则比较
            return $this->compareReg($rule, $uriarr);
        }

        // 需要用正则路由规则判断
        if ($this->isRegUri) {
            return false;
        }

        // 字符串比较
        return $this->compareString($rule, $uriarr);
    }

    // 正则匹配
    protected function compareReg(& $rule, & $uriarr)
    {
        $uri = $this->getUri();

        foreach ($rule['pattern'] as $k => & $p) {
            if ($this->hasReg($p)) {
                if (! preg_match($p, $uri, $data)) {
                    return false;
                }
            } else {
                if (strpos($p, $this->signOfAny) === false && strtolower($p) != strtolower($uriarr[$k])) {
                    return false;
                }
            }
        }

        $this->regResultData = & $data;

        return true;
    }



    // 字符串比较
    protected function compareString(& $rule, & $uriarr)
    {
        foreach ($rule['pattern'] as $k => & $p) {
            if (strpos($p, $this->signOfAny) === false && strtolower($p) != strtolower($uriarr[$k])) {
                return false;
            }
        }
        return true;
    }

    // 判断是否含有正则字符
    protected function hasReg($str)
    {
        return (strpos($str, $this->regularSymbol) === false) ? false : true;
    }

    public function getUri()
    {
        if ($this->requestUri) {
            return $this->requestUri;
        }
        //$uri = str_replace("\\", '/', $_SERVER['REQUEST_URI']);
        $uri = $_SERVER['REQUEST_URI'];

        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }

        if (strpos($uri, $this->regularSymbolByUri) !== false) {
            $this->isRegUri = true;
        }

        $this->requestUri = $uri;

        return $uri;
    }

    // 设置模块
    protected function setModule(array & $rule)
    {
        // 优先判断路由配置中的"module"参数
        $this->module = get_value($rule, 'module');
        if ($this->module) {
            return;
        }

        // 判断是否开启了子域名部署
        if (config('domain-deploy')) {
            // 是
            $module = store('host.module');

            if ($module) {
                $this->module = & $module;
                return;
            }
        }

        $this->module = (array) config('modules');
        if ($this->module) {
            $this->module = $this->module[0];
        }
    }


    // 获取路由解析结果
    public function getDispatchResult()
    {
        return $this->matchResult;
    }

    // 获取路由规则
    public function getRouterRules()
    {
        return $this->config;
    }

    // 去除空值并重置key  array_values(array_filter($arr))
    protected function arrayFilter(& $arr)
    {
        $new = [];
        foreach ($arr as & $row) {
            if (! $row) {
                continue;
            }
            $new[] = $row;
        }
        $arr = $new;
    }

}
