<?php
/**
 * Js接口
 *
 * @author Jqh
 * @date   2017/7/3 10:30
 */

namespace Lxh\Admin\Controller;

use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\MVC\Controller;

class Js extends Controller
{
    /**
     * 静态资源路径前缀
     *
     * @var string
     */
    protected $rootPrefix;

    /**
     * 模板路径前缀
     *
     * @var string
     */
    protected $tplPrefix;

    public function __construct()
    {
        $this->rootPrefix = __PUBLIC_ROOT__ . 'static/' . $GLOBALS['resource-version'] . '/';
        $this->tplPrefix = $this->rootPrefix . 'tpl/' . __MODULE__ . '/';

        // 禁止输出控制台调试信息
        $this->withConsoleOutput(false);
    }

    /**
     * 获取js入口
     * 返回一段js代码
     *
     * @return string
     */
    public function actionEntrance(Request $req, Response $resp, & $params)
    {
        if (empty($params['type'])) {
            return;
        }

        $method = 'load' . rtrim($params['type'], '.js');

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
    }

    /**
     * 载入js数据
     *
     * @return void
     */
    protected function loadData()
    {
        $options = I('n');

        if (! is_array($options)) {
            return $this->jsCall();
        }

        $js = [];
        foreach ($options as & $param) {
            $param = explode(':', $param);

            if (count($param) < 2) {
                continue;
            }

            $method = 'loadData' . $param[0];

            if (method_exists($this, $method)) {
                $js[$param[0]] = $this->{$method}(explode(',', $param[1]));
            }
        }

        return $this->jsCall($js);
    }

    /**
     * 返回js字符串
     *
     * @return string
     */
    protected function jsCall(& $data = '')
    {
        $json = json_encode((array) $data);

        return <<<EOF
window.load_data = function () {
    return $json
}
EOF;

    }

    /**
     * 加载语言包
     *
     * @param  array $scopes
     * @return void
     */
    protected function loadDataLanguage(array $scopes)
    {
        return language()->getPackages($scopes);
    }

    /**
     * 加载模板数据
     *
     * @return array
     */
    protected function loadDataTpl(array $filenames)
    {
        $data = [];
        foreach ($filenames as & $name) {
            $path = $this->getTplPath($name);
            if (is_file($path)) {
                $data[$name] = file_get_contents($path);
            }
        }
        return $data;
    }

    /**
     * 获取模板路径
     *
     * @return string
     */
    protected function getTplPath($name)
    {
        return $this->tplPrefix . str_replace('.', '/', $name) . '.html';
    }

    // 加载语言包
    protected function loadLanguage()
    {
        $scopes = explode(',', I('scopes'));

        if (empty($scopes)) {
            return;
        }

        $lang = I('lang', 'en');

        $data = json_encode(
            language()->getPackages($scopes, $lang)
        );

        return "window.languagePackages = {$data};";
    }
}

