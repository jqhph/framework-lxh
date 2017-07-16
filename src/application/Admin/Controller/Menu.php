<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controller;

use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Menu extends Controller
{
    public function __construct()
    {
    }


    public function actionIndex()
    {
        return fetch_complete_view();
    }

}
