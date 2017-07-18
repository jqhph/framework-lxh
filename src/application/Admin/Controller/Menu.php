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
     * @return bool
     */
    protected function updateValidate(array & $fields, Validator $validator)
    {

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

        assign('row', $row);
        assign('menus', $menus);

        return fetch_complete_view();
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

        assign('titles', $titles);
        assign('list', $list);


        return fetch_complete_view();
    }

}
