<?php

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\ApiTest\Manager;
use Lxh\Admin\Http\Controllers\Controller;
use Lxh\Admin\Widgets\Form;

class ApiRequest extends Controller
{
//    public function form(Form $form)
//    {
//
//    }

    public function actionTest()
    {
        $tester = new Manager($this->admin());

        return $tester->render();
    }
}
