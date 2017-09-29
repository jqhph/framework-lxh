<?php

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\Helper\Util;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Status;

class Test extends Controller
{
    public function actionTest(Request $req, Response $resp, & $params)
    {
        $data = [
            ['name������ᶫ�ɷݵ�?'],
            ['name'],
            ['name'],
            ['name'],
        ];


    }



    public function actionHello()
    {

    }

}
