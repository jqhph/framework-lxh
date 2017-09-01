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
        $data = [
            'a' => '123ff',
            343 => 'yyy',
            'f' => [
                'name' => 22,
                'attr' => [
                    'ur' => false
                ]
            ],
        ];

        console_log('data', Util::arrayToText($data));

        return debug(Util::arrayToText($data));
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
