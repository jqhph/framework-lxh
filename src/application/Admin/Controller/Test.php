<?php

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\Exception;
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

        return $this->render('test', ['title' => 'HELLO']);
//        $test = new \Lxh\Mails\Test();
//
//        $this->mailer->to('841324345@qq.com')->send($test);
//
//        return $test;

    }

    public function actionHello()
    {

    }

}
