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
    protected function initialize()
    {
        
    }

    public function actionList()
    {
        $this->withConsoleOutput(false);

        return $this->render('public.public');
    }

    public function actionIndex()
    {
        return $this->render('index', [], true);
    }

}
