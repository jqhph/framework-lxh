<?php
/**
 *
 * @author {author}
 * @date   {date}
 */

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\Helper\Util;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Test extends Controller
{
    public function actionTest(Request $req, Response $resp)
    {
        $data = 'suitshe.com';
        $host = 'www.suitshe.com';

        console_log(substr_count($data, '.'), substr_count($host, '.'));
    }

    public function actionHello()
    {
        return $_SERVER;
    }

    public function actionTestApi()
    {
        $client = new \Lxh\Http\Kc();

        $r = file_get_contents('http://dev.lxh.com/Test/Hello');

        $a = '';


        return ['result' => '', 'a' => explode(',', $a)];
    }
}
