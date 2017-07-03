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
            return $v->errors();
        }

        return $this->success();
    }
}
