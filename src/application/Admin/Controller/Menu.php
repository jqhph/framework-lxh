<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\Forbidden;
//use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;

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
    protected function updateValidate($id, array & $fields, Validator $validator)
    {
        if ($fields['parent_id'] == $id) {
            return 'Can\'t put self as a parent';
        }

        $validator->rule('required', ['name', 'priority']);

        $validator->rule('lengthBetween', 'icon', 4, 30);

        $validator->rule('lengthBetween', 'name', 4, 30);

        $validator->rule('lengthBetween', 'controller', 1, 15);

        $validator->rule('lengthBetween', 'action', 1, 15);

        $validator->rule('required', 'parent_id');

        $validator->rule('integer', 'priority');
    }

    // 删除操作验证方法
    public function deleteValidate($id)
    {
        // 判断是否是系统菜单，如果是则不允许删除
        if ($this->getModel()->isSystem($id)) {
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

        $menus = make('acl-menu')->all();

        array_unshift($menus, ['id' => 0, 'name' => trans('Top level'), 'required' => 1]);

        $this->share('navTitle', $currentTitle);

        return $this->render('detail', ['menus' => & $menus], true);
    }

    /**
     * 详情页
     *
     * @return array
     */
    public function actionDetail(Request $req, Response $resp, & $params)
    {
        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $model = $this->getModel();

        $model->id = $id;

        $row = $model->find();

        $menus = make('acl-menu')->all();

        $currentTitle = 'Modify menu';

        array_unshift($menus, ['id' => 0, 'name' => trans('Top level'), 'required' => 1]);

        $this->share('navTitle', $currentTitle);

        return $this->render(
            'detail',
            ['row' => & $row, 'menus' => & $menus, ],
            true
        );
    }

    public function actionList()
    {
        $titles = [
            'id' => ['priority' => 0,],
            'icon' => [
                'view' => 'fields/icon/list'
            ],
            'name' => [
            ],
            'controller' => [
            ],
            'action' => [
            ],
            'show' => [
                'view' => 'fields/bool/list'
            ],
            'type' => [
                'view' => 'fields/enum/list'
            ],
            'priority' => [
            ],
        ];

        $list = make('acl-menu')->all();

        $this->share('titles', $titles);

        return $this->render('list', ['list' => & $list], true);
    }

}
