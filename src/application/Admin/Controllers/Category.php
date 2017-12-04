<?php
/**
 *
 * @author Jqh
 * @date   2017-10-17 19:37:31
 */

namespace Lxh\Admin\Controllers;

use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Category extends Controller
{
    protected $maxSize = 20;

    protected $loadJs = false;

    public function initialize()
    {
    }

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
            'desc' => [],
            'created_at' => ['view' => 'date/list'],
            'modified_at' => ['view' => 'date/list'],
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
        return [
            [
                ['view' => 'varchar/search', 'vars' => ['name' => 'name']],
                ['view' => 'varchar/date-search', 'vars' => ['name' => 'created_at']],
            ],
        ];
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function makeDetailItems($id = null)
    {
        return [
            ['view' => 'varchar/edit', 'vars' => ['name' => 'name', 'labelCol' => 1, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'desc', 'labelCol' => 1, 'formCol' => 9]],
            ['view' => 'date/edit', 'vars' => ['name' => 'created_at', 'labelCol' => 1, 'formCol' => 9, 'disabled' => true]],
            ['view' => 'date/edit', 'vars' => ['name' => 'modified_at', 'labelCol' => 1, 'formCol' => 9, 'disabled' => true]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'created_by', 'labelCol' => 1, 'formCol' => 9, 'disabled' => true]],

        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|lengthBetween:2,30'
        ];
    }

    protected function updateValidate($id, array & $fields)
    {
        unset($fields['created_at'], $fields['modified_at'], $fields['created_by_id'], $fields['created_by']);
    }


    // 前端字段验证规则
    protected function makeClientValidatorRules()
    {
        return [
            ['name' => 'name', 'rules' => 'required|length_between[2-30]']
        ];
    }

}
