<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Admin\Controller;

use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Index extends Controller
{
    public function __construct()
    {
    }

    public function actionCall()
    {
        return ['I am call'];
    }

    public function actionIndex()
    {

        return fetch_complete_view();
    }

    public function addMiddleware(array $allMiddleware, $m)
    {
        $data = [];
        foreach ($allMiddleware as $module => & $middlewares) {
            if ($module == '*' || $module == $m) {
                $data = array_merge($data, $middlewares);
            }
        }
        return $data;
    }

}
