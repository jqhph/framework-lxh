<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Grid;
use Lxh\Exceptions\Forbidden;
//use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;
use Lxh\Admin\Layout\Row;

class Menu extends Controller
{
    protected function initialize()
    {
    }

    /**
     * 修改前字段验证
     *
     * @param  array
     * @return mixed
     */
    protected function updateValidate($id, array & $fields)
    {
        if ($fields['parent_id'] == $id) {
            return 'Can\'t put self as a parent';
        }
    }

    /**
     * 表单字段验证规则
     *
     * @return void|array
     */
    protected function rules()
    {
        return [
            'name' => 'required',
            'priority' => 'required|integer',
            'icon' => 'lengthBetween:4,30',
            'controller' => 'lengthBetween:1,15',
            'action' => 'lengthBetween:1,15',
            'parent_id' => 'required'
        ];
    }

    // 删除操作验证方法
    public function deleteValidate($id)
    {
        // 判断是否是系统菜单，如果是则不允许删除
        if ($this->model()->isSystem($id)) {
            return 'Can\'t delete the system menu!';
        }
    }

    /**
     * 新增操作界面
     *
     * @return string
     */
    public function actionCreate(Request $req, Response $resp, & $params)
    {
        $currentTitle = 'Create Menu';

        $menus = resolve('acl-menu')->all();

        array_unshift($menus, ['id' => 0, 'name' => trans('Top level'), 'required' => 1]);

        $this->share('navTitle', $currentTitle);

        return $this->render('detail', ['menus' => & $menus], true);
    }

    /**
     * 详情页
     *
     * @return array
     */
    public function actionDetail(Request $req, Response $resp, array & $params)
    {
        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $model = $this->model();

        $model->id = $id;

        $row = $model->find();

        $menus = resolve('acl-menu')->all();

        $currentTitle = 'Modify menu';

        array_unshift($menus, ['id' => 0, 'name' => trans('Top level'), 'required' => 1]);

        $this->share('navTitle', $currentTitle);

        return $this->render(
            'detail',
            ['row' => & $row, 'menus' => & $menus, ],
            true
        );
    }

    public function actionList(Request $req, Response $resp, array & $params)
    {
        $content = $this->admin()->content();

        $content->row(function (Row $row) {
            $row->column(12, $this->buildGrid());
        });

        return $content->render();
    }

    protected function buildGrid()
    {
//        ddd(resolve('acl-menu')->all());
        $grid = new Grid([
            'id' => [
                'priority' => 0,
            ],
            'icon' => ['view' => 'Icon'],
            'name' => [],
            'controller' => [],
            'action' => [],
            'show' => ['view' => 'Boolean'],
            'type' => ['view' => 'Enum'],
            'priority' => [],
        ],  resolve('acl-menu')->all());

        $grid->table()->useTree('subs');
        $grid->usePagination(false);

        return $grid;
    }

}
