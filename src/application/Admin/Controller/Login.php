<?php
/**
 * 登录管理控制器
 *
 * @author Jqh
 * @date   2017/6/27 16:59
 */

namespace Lxh\Admin\Controller;

use Lxh\Helper\Util;
use Lxh\Mails\Test;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Login extends Controller
{
    public function actionIndex(Request $request, Response $response, $params)
    {
//        console_info(Util::toUnderScore('myAccountTest'));
//        assign('test', $a);
//        return $this->render('index');

        $test = new Test();

        $this->container['mailer']->to('841324345@qq.com')->send($test);

        return $test->render();
    }

    public function actionRegister()
    {
        return $this->render('register');
    }
}
