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
use Lxh\Mvc\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Lxh\RequestAuth\Exceptions\AuthTokenException;

class Login extends Controller
{
    public function actionIndex($params)
    {
        try {
            if (__admin__()->auth()->check()) {
                $this->response->redirect(
                    Admin::url()->index()
                );
                return;
            }
        } catch (AuthTokenException $e) {

        }
        // 是否需填写验证码
        $requiredCaptcha = session()->get('is_required_captcha');

        return $this->content()
            ->page(true)
            ->body(
                $this->render('index', ['requiredCaptcha' => $requiredCaptcha])
            )
            ->render();
    }

    public function actionCaptcha(array $params)
    {
        $phrase = Util::randomString(5);

        session()->save('_captcha', ['code' => $phrase, 'at' => time()]);

        $builder = new CaptchaBuilder($phrase);
        $builder->setBackgroundColor(255, 222, 173);

        $builder->build();

        $this->response->withHeader('Content-type', 'image/jpeg');

        return $builder->output();
    }

    public function actionRegister()
    {
        return $this->render('register');
    }
}
