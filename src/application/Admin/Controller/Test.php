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

        $qrCode = new QrCode('10013');

        $resp->withHeader('Content-Type', $qrCode->getContentType());

        return $qrCode->writeString();
    }

    public function actionHello()
    {

    }

}
