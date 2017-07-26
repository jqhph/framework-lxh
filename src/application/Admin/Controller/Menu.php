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
    public function __construct()
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

        array_unshift($menus, ['id' => 0, 'name' => trans('Top level'), 'required' => 1]);

        assign('navTitle', $currentTitle);

        return fetch_complete_view('Detail', ['menus' => make('acl-menu')->all()]);
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

        $row = query()->from('Menu')->where('id', $id)->findOne();

        $menus = make('acl-menu')->all();

        $currentTitle = 'Modify menu';

        array_unshift($menus, ['id' => 0, 'name' => 'Top level', 'required' => 1]);

        assign('navTitle', $currentTitle);

        return fetch_complete_view(__ACTION__, [
            'row' => & $row,
            'menus' => & $menus,
        ]);
    }

    public function actionIndex()
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

        return fetch_complete_view('Index', ['titles' => & $titles, 'list' => & $list]);
    }

}
