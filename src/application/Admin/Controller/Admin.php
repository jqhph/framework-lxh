<?php
/**
 * User controller
 *
 * @author Jqh
 * @date   2017/6/28 21:34
 */

namespace Lxh\Admin\Controller;

use Lxh\Helper\Arr;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Admin extends Controller
{
    /**
     * 用户登录api
     *
     * @return string
     */
    public function actionLogin(Request $req, Response $resp)
    {
        if (empty($_POST)) {
            return $this->error();
        }
        $v = $this->validator();

        $v->fill($_POST);

        $v->rule('username', 'lengthBetween', 4, 20);

        $v->rule('password', 'lengthBetween', 4, 30);

        if (! $v->validate()) {
            return $this->error($v->errors());
        }

        if (! $this->getModel()->login($_POST['username'], $_POST['password'], I('remember'))) {
            return $this->failed();
        }

        return $this->success();
    }


    public function actionRegister(Request $req, Response $resp)
    {
        if (empty($_POST)) {
            return $this->error();
        }
        $v = $this->validator();

        $v->fill($_POST);

        $v->rule('username', 'lengthBetween', 4, 20);

        $v->rule('password', 'lengthBetween', 4, 30);

        $v->rule('password', 'equals', 'repassword');

        if (! $v->validate()) {
            return $this->error($v->errors());
        }

        $admin = $this->getModel();

        if ($admin->userExists($_POST['username'])) {
            return $this->error('The username exists.');
        }

        if (! $admin->register($_POST, $req->ip())) {
            return $this->failed();
        }

        $admin->login($_POST['username'], $_POST['password'], true, true);

        return $this->success();
    }
}
