<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Util;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Mvc\Controller as Base;
use Lxh\Status;

class Error extends Base
{
    protected function initialize()
    {
    }

    /**
     * 404
     *
     * @return string
     */
    public function actionNotFound()
    {
        $this->response->withStatus(404);

        return view('admin::error.404')->render();
    }

    /**
     * 500
     *
     * @return string
     */
    public function actionServerError()
    {
        $this->response->withStatus(500);

        return view('admin::error.500')->render();
    }
}
