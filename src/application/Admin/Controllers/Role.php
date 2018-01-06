<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:57
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\MVC\Controller;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Form;
use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;

class Role extends Controller
{
    protected $filter = true;

    protected function grid(Grid $grid, Content $content)
    {
        $grid->allowBatchDelete();
    }

    protected function table(Table $table)
    {
        $table->field('id')->hide()->sortable();
        $table->field('title');
        $table->field('name');
        $table->field('comment');
        $table->field('created_at')->view('date')->sortable();
        $table->field('modified_at')->view('date')->sortable();
        $table->field('created_by');
    }

    protected function filter(Filter $filter)
    {
        $filter->useModal();
        $filter->text('name')->like();
        $filter->text('title')->like();
        $filter->dateRange('created_at')->between()->toTimestamp();
    }

    protected function form(Form $form, Content $content)
    {
        $form->text('title')->rules('required|length_between[2-30]');
        $form->text('name')->rules('required|length_between[2-10]');
        $form->text('comment');
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
//    protected function makeDetailItems($id = null)
//    {
//        $permissions = ['menus' => [], 'custom' => []];
//        if ($id) {
//            $permissions = $this->model('Role')->getPermissions($id);
//        }
//
//        $menuList = make('acl-menu')->permissionsList($permissions['menus']);
//
//        return [
//            ['view' => 'varchar/edit', 'vars' => ['name' => 'name', 'labelCol' => 1, 'formCol' => 9]],
//            [
//                'view' => 'checkbox/items-edit',
//                'vars' => [
//                    'name' => 'permissions',
//                    'labelCol' => 1,
//                    'formCol' => 9,
//                    'labelCategory' => 'menus',
//                    'columns' => 6,
//                    'list' => & $menuList,
//                ]
//            ],
//        ];
//    }

    protected function updateFilter($id, array &$fields)
    {
        if (empty($fields['permissions'])) {
            return 'The permissions fields is required';
        }
    }

    // 字段验证规则
    protected function rules()
    {
//        return [
//            'name' => 'required|between:2,30',
//            'title' => 'required|between:2,30',
//        ];
    }

}
