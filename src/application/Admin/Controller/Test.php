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
        $data = [1, 2];
        $b = [3, 4];

        debug($data + $b);
        die;
        
        $crawler = new \Lxh\Kernel\Spiders\Crawler();

//        $crawler->makeProdsData();
        $crawler->makeCategoriesData();

        $crawler->outputRequestResult();
//return $sorts;
//        debug($sorts);die;

//        return '<pre>' . Util::arrayToText($sorts);
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
