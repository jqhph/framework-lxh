<?php

namespace Lxh\Admin\Controllers;

use Lxh\Debug\Code;
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
        $s = "sd=9&f=5";
        parse_str($s, $d);
        ddd($d);
        ddd(123, 456);
        $client = resolve('http.client');
        $client->post('https://graph.facebook.com/v2.6/me/messages?access_token=')->then();

        ddd($client->response());
//        query()->from('test1')->find();
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
