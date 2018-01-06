<?php
/**
 * 登录管理控制器
 *
 * @author Jqh
 * @date   2017/6/27 16:59
 */

namespace Lxh\Admin\Controllers;

use Lxh\Helper\Util;
use Lxh\Mails\Test;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Login extends Controller
{
    public function actionIndex(Request $request, Response $response, $params)
    {
        $content = $this->admin()
            ->content()
            ->independent();

            $content->body(
                $this->render('index')
            );
        return $content->render();

        return $this->render('index');
    }

    public function actionRegister()
    {
        return $this->render('register');
    }
}
