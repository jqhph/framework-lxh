<?php
/**
 * User controller
 *
 * @author Jqh
 * @date   2017/6/28 21:34
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\MVC\Controller;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Admin extends Controller
{
    /**
     * @var string
     */
    protected $filter = 'modal';

    /**
     * @param Grid $grid
     * @param Content $content
     */
    public function grid(Grid $grid, Content $content)
    {
    }

    public function table(Table $table)
    {
        $table->field('id')->sortable();
        $table->field('username');
        $table->field('email');
        $table->field('mobile');
        $table->field('status')->view('Boolean');
        $table->field('is_admin')->view('Boolean')->hide();
        $table->field('sex')->view('Select');
        $table->field('created_at')->view('Date')->sortable();
        $table->field('modified_at')->view('Date')->sortable()->hide();
        $table->field('last_login_ip')->hide();
        $table->field('last_login_time')->view('Date')->hide();

        $table->column(3, 'name', function (array $row, Td $td, Th $th, Tr $tr) {
            return $row['first_name'] . $row['last_name'];
        });
        $table->column(6, 'roles', function (array $row, Td $td, Th $th, Tr $tr) {
        });
    }

    public function filter(Filter $filter)
    {
        $filter->text('username')->like();
        $filter->text('email')->like()->right();
        $filter->text('mobile')->like()->right();
        $filter->text('name')->where(function () {
            if (! $value = I('name')) {
                return null;
            }
            $like = ['LIKE', "$value%"];

            return [
                'OR' => ['first_name' => &$like, 'last_name' => &$like]
            ];
        })->formatField(false);
    }

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

        if (! $this->model()->login($_POST['username'], $_POST['password'], I('remember'))) {
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

        $admin = $this->model();

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
