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

    // 加载语言包
    protected function loadLanguage()
    {
        $scopes = explode(',', I('scopes'));

        $lang = I('lang', 'en');

        $l = language();

        if ($scopes) {
            foreach ($scopes as & $s) {
                $l->loadPackage($s, $lang);
            }
        }

        $data = $l->all();

        if (! in_array('Global', $scopes)) {
            unset($data[$lang]['Global']);
        }

        $data = json_encode($data);

        return "window.languagePackages = {$data};";
    }
}

