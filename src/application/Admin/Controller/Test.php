<?php

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\Helper\Util;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Status;
use Endroid\QrCode\QrCode;

class Test extends Controller
{
    public function actionTest(Request $req, Response $resp, & $params)
    {

        $post = [
            'name' => '1231234',
            'email' => 'd@qq.com'
        ];

        $rules = [
            'name' => 'required|lengthBetween:3,7',
            'email' => 'required|email'
        ];

        $v = resolve('validator');

        $v->fill($post)->rules($rules);

        // 验证并获取结果
        if (! $v->validate()) {
            return $v->errors();
        }

        return 'success';
//        $qrCode = new QrCode('10013');
//
//        $resp->withHeader('Content-Type', $qrCode->getContentType());
//
//        return $qrCode->writeString();
    }

    public function actionHello()
    {

    }

}
