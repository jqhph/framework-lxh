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
        $data = 'fjjJ';
        $data2 = 'dfdg ';

        $r1 = preg_match('/^[a-z\-]+$/', $data);
        $r2 = preg_match('/^[a-z\-]+$/', $data2);

        var_dump($r1);
        var_dump($r2);

        return $this->render('test', ['title' => 'Hello Lxh!', 'list' => ['m', 'v', 'c']]);

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
