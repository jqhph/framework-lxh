<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Admin\Controller;

use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Index extends Controller
{
    public function __construct()
    {
    }


    public function actionList()
    {
        return fetch_complete_view();
    }

}
