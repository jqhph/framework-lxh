<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:57
 */

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;

class Role extends Controller
{
    /**
     * 获取list页table标题信息
     *
     * @return array
     */
    protected function getListTableTitles()
    {
        return [];
    }

    /**
     * 获取搜索项
     *
     * @return array
     */
    protected function getSearchItems()
    {
        return [];
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function getDetailFields()
    {
//        debug(make('acl-menu')->permissionsList());die;
        return [
            ['view' => 'varchar/edit', 'vars' => ['name' => 'name', 'labelCol' => 1, 'formCol' => 9]],
            ['view' => 'checkbox/items-edit', 'vars' => [
                'name' => 'permissions', 'labelCol' => 1, 'formCol' => 9, 'labelCategory' => 'menus', 'columns' => 6,
                'list' => make('acl-menu')->permissionsList(), ]
            ],
        ];
    }

}
