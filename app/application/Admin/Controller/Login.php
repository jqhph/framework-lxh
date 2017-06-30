<?php
/**
 * 登录管理控制器
 *
 * @author Jqh
 * @date   2017/6/27 16:59
 */

namespace Lxh\Admin\Controller;

use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Login extends Controller
{
    public function actionIndex(Request $request, Response $response, $params)
    {
        console_info($params, 213213, 12321321,4354356345, 234234234234234);
        
//        assign('test', $a);
        return fetch_view();
    }
}
