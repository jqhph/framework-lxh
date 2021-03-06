<?php
/**
 * Js接口
 *
 * @author Jqh
 * @date   2017/7/3 10:30
 */

namespace Lxh\Admin\Http\Controllers;

use Lxh\Mvc\Controller;

class Js extends Controller
{
    protected function initialize()
    {
        // 禁止输出控制台调试信息
        $this->withConsoleOutput(false);
    }

    /**
     * 获取js入口
     * 返回一段js代码
     *
     * @return string
     */
    public function actionEntrance(array $params)
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
window.load_data = function () {return $json;};
EOF;

    }

    /**
     * 加载语言包
     *
     * @param  array $scopes
     * @return array
     */
    protected function loadDataLanguage(array $scopes)
    {
        $packages = translator()->getPackages($scopes);

        foreach ($packages as $lang => &$package) {
            foreach ($scopes as &$scope) {
                if (empty($package[$scope])) {
                    $package[$scope] = [false];
                }
            }
        }

        return $packages;
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
            translator()->getPackages($scopes, $lang)
        );

        return "window.languagePackages = {$data};";
    }
}

