<?php
/**
 * 登录管理控制器
 *
 * @author Jqh
 * @date   2017/6/27 16:59
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Helper\Util;
use Lxh\Mails\Test;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Gregwar\Captcha\CaptchaBuilder;

class Login extends Controller
{
    public function actionIndex($params)
    {
        if (admin()->oauth()->check()) {
            return $this->response->redirect(
                Admin::url()->index()
            );
        }

        return $this->content()
            ->independent()
            ->body(
                $this->render('index')
            )
            ->render();
    }

    public function actionCaptcha(array $params)
    {
        $builder = new CaptchaBuilder('test123');
        $builder->build();

        $this->response->withHeader('Content-type', 'image/jpeg');

        return $builder->output();
    }

    public function actionRegister()
    {
        return $this->render('register');
    }
}
