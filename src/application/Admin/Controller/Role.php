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
    protected $maxSize = 20;
    /**
     * 获取list页table标题信息
     *
     * @return array
     */
    protected function makeListItems()
    {
        return [
            'id' => ['priority' => 0,],
            'name' => [],
            'permissions' => ['view' => 'permit/btn'],
            'created_at' => ['view' => 'varchar/date-list'],
            'modified_at' => ['view' => 'varchar/date-list'],
            'created_by' => [],
        ];
    }

    /**
     * 获取搜索项
     *
     * @return array
     */
    protected function makeSearchItems()
    {
        return [];
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function makeDetailItems($id = null)
    {
        $permissions = ['menus' => [], 'custom' => []];
        if ($id) {
            $permissions = $this->getModel('Role')->getPermissions($id);
        }

        $menuList = make('acl-menu')->permissionsList($permissions['menus']);

        return [
            ['view' => 'varchar/edit', 'vars' => ['name' => 'name', 'labelCol' => 1, 'formCol' => 9]],
            [
                'view' => 'checkbox/items-edit',
                'vars' => [
                    'name' => 'permissions',
                    'labelCol' => 1,
                    'formCol' => 9,
                    'labelCategory' => 'menus',
                    'columns' => 6,
                    'list' => & $menuList,
                ]
            ],
        ];
    }

    protected function updateValidate($id, array & $fields, Validator $validator)
    {
        if (empty($fields['permissions'])) {
            return 'The permissions fields is required';
        }
    }

    // 前端字段验证规则
    protected function makeClientValidatorRules()
    {
        return [
            ['name' => 'name', 'rules' => 'required|length_between[2-30]']
        ];
    }

}
