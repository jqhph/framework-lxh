<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Home\Controller;

use Lxh\MVC\Controller;

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
        $all = [
            '*' => [
                'test1*', 'test2*'
            ],
            'Home' => [
                'home1', 'home2'
            ]
        ];



        return $this->addMiddleware($all, 'Home');
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
