<?php
/**
 * User controller
 *
 * @author Jqh
 * @date   2017/6/28 21:34
 */

namespace Lxh\Admin\Controller;

use Lxh\Http\Request;
use Lxh\Http\Response;

class User extends Controller
{
    public function actionLogin(Request $req, Response $resp)
    {
        $v = $this->validator();
        
        $v->fill($_POST);

        $v->rule('lengthBetween', 'username', 4, 20);

        $v->rule('lengthBetween', 'password', 4, 30);

        if (! $v->validate()) {
            // Errors
            return $this->error($v->errors());
        }

        return $this->success();
    }
    
    public function actionRegister(Request $req, Response $resp)
    {
        $v = $this->validator();
        
        $v->fill($_POST);
        
        $v->rule('lengthBetween', 'username', 4, 20);
        
        $v->rule('lengthBetween', 'password', 4, 30);

        $v->rule('equals', 'password', 'repassword');

        if (! $v->validate()) {
            // Errors
            return $this->error($v->errors());
        }

        $user = $this->getModel('User');

//        if (! $this->getModel('User')->register($_POST, $req->ip())) {
//            return $this->failed();
//        }

        $user->login($_POST['username'], $_POST['password'], true, true);
        
        return $this->success();
    }
}
