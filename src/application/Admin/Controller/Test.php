<?php
/**
 *
 * @author {author}
 * @date   {date}
 */

namespace Lxh\Admin\Controller;

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

        $data = pdo()->prepare('SELECT * FROM user WHERE id = :id AND is_admin = :is_admin', [':id' => 1, ':is_admin' => 1])->fetchAll(\PDO::FETCH_ASSOC);

        assign('data', $data);

        return fetch_view();

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
