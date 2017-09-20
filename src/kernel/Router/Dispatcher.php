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
    protected $requestPath;

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
     * 匹配任意路由规则符号
     *
     * @var string
     */
    protected $anySymbol = '*';

    /**
     * uri正则匹配符号
     *
     * @var string
     */
    protected $regularSymbolByUri = '-';

    protected $defaultMethod = 'GET';

    /**
     * 路由钩子
     */
    protected $hooks = [];

    /**
     * 路由解析结果
     *
     * @var bool
     */
    protected $matchResult;

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

    /**
     * 特殊字符串匹配规则
     *
     * @var array
     */
    protected $patternStrings = [
        '#numbers' => '/^[0-9]+$/',
        '#letters' => '/^[a-z\-_]+$/i',
        '#lowercase' => '/^[a-z\-]+$/',
    ];

    /**
     * Dispatcher constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = & $config;

    }

    // 添加路由规则配置
    public function add(array $config)
    {
        $this->config[] = & $config;
    }

    // 设置路由规则配置
    public function fill(array $config)
    {
        $this->config = & $config;
    }

    /**
     * 开始匹配路由
     *
     * @return bool
     */
    public function handle()
    {
        // 解析路由
        return $this->matchResult = $this->dispatch();

    }

    /**
     * 解析路由
     *
     * @return string
     */
    protected function dispatch()
    {
        $patharr = $this->getPathArr();

        $pathlen = count($patharr);

        // 匹配路由
        foreach ($this->getRouterRules() as & $rule) {
            if (empty($rule['pattern'])) continue;

            if ($this->matchingPattern($rule, $patharr, $pathlen)) {
                // 匹配成功，保存路由信息
                $this->save($rule, $patharr);
                return true;
            }
        }

        return false;
    }

    protected function getPathArr()
    {
        $patharr = explode('/', $this->getPath());

        $this->arrayFilter($patharr);

        return $patharr;
    }

    /**
     * 路由匹配成功，缓存相关数据
     *
     * @param array $rule
     * @param array $patharr
     * @return void
     */
    protected function save(array & $rule, array & $patharr)
    {
        // 验证配置
        $contr = $action = '';

        $params = [];// 参数

        foreach (get_value($rule, 'params', []) as $k => & $p) {
            switch ($k) {
                case 'controller':
                    $contr = & $p;
                    break;
                case 'action':
                    $action = & $p;
                    break;
                case 'module':
                    $this->module = & $p;
                    break;
                case 'auth':
                    $this->auth = & $p;
                    break;
                default:
                    $params[$k] = & $p;
            }
        }

        $realContr = $realAction = '';
        foreach ((array) $rule['pattern'] as $k => & $r) {
            if ($r == $contr) {
                $realContr = & $patharr[$k];
            }

            if ($r == $action) {
                $realAction = & $patharr[$k];
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
                    $param = $patharr[$k];
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
        $this->requestParams  = & $params;
    }

    /**
     * 匹配路由模式
     *
     * @return bool
     */
    protected function matchingPattern(& $rule, & $patharr, $pathlen)
    {
        if ($rule['pattern'] === $this->anySymbol) {
            return true;
        }

        // 判断是否含有正则字符串
        $hasReg = $this->hasReg($rule['pattern']);

        // 路由模式
        $rule['pattern'] = explode('/', $rule['pattern']);

        $this->arrayFilter($rule['pattern']);

        if ($pathlen != count($rule['pattern'])) {
            return false;
        }

        // 请求方法匹配，默认GET方法
        $method = get_value($rule, 'method', $this->defaultMethod);
        if ($method != $this->anySymbol && (strpos($method, $_SERVER['REQUEST_METHOD']) === false)) {
            return false;
        }

        if ($hasReg) {
            // 正则比较
            return $this->compareReg($rule, $patharr);
        }

        // 字符串比较
        return $this->compareString($rule, $patharr);
    }

    // 正则匹配
    protected function compareReg(& $rule, & $patharr)
    {
        $uri = $this->getPath();

        foreach ($rule['pattern'] as $k => & $p) {
            if ($this->hasReg($p)) {
                if (! preg_match($p, $uri, $data)) {
                    return false;
                }
            } else {
                if (strpos($p, $this->signOfAny) === false && $p != $patharr[$k]) {
                    return false;
                }
            }
        }

        $this->regResultData = & $data;

        return true;
    }

    // 字符串比较
    protected function compareString(& $rule, & $patharr)
    {
        foreach ($rule['pattern'] as $k => & $p) {
            // 特殊意义字符匹配
            if (isset($this->patternStrings[$p])) {
                if (! preg_match($this->patternStrings[$p], $patharr[$k])) {
                    return false;
                }
                continue;
            }

            if (strpos($p, $this->signOfAny) === false && $p != $patharr[$k]) {
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

    public function getPath()
    {
        if ($this->requestPath) {
            return $this->requestPath;
        }

        $uri = get_value($_SERVER, 'PATH_INFO', '/');

        return $this->requestPath = & $uri;
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
            if (empty($row)) continue;

            $new[] = $row;
        }
        $arr = $new;
    }

}
