<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 23:28
 */

namespace Lxh\Admin\Controller;

use Lxh\Http\Request;
use Lxh\Http\Response;

class Language extends Controller
{
    /**
     * 获取语言包数据接口
     *
     * @param Request $req
     * @param Response $resp
     * @return array
     */
    public function actionGet(Request $req, Response $resp)
    {
        $scopes = explode(',', I('scopes'));

        if (empty($scopes)) {
            return $this->success('SUCCESS', ['list' => []]);
        }

        $lang = I('lang', 'en');

        return $this->success('SUCCESS', ['list' => language()->getPackages($scopes, $lang)]);
    }
}
