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

        return $this->success('SUCCESS', ['list' => & $data]);
    }
}
