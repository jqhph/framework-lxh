<?php
/**
 *
 * @author {author}
 * @date   {date}
 */

namespace Lxh\Home\Controller;

use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Test extends Controller
{
    public function actionHi(Request $req, Response $resp)
    {
        $data = [];

        $s = microtime(true);

        $a = [];

//        $r = query()->from('user')->where('id', 18)->find();

        $a['a'][] = $req->date();

        console_info('test a', $a);

        // 输出内容到控制台
        console_info(55555, 'fuck');

        assign('data', $a);

        return fetch_complete_view();

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
