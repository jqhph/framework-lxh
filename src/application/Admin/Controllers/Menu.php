<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\MVC\Controller;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;
use Lxh\Admin\Layout\Row;

class Menu extends Controller
{
    /**
     * 网格字段定义
     *
     * @var array
     */
    protected $grid = [
        'id' => ['show' => 0, 'sortable' => 1, 'desc' => 1],
        'icon' => ['view' => 'Icon'],
        'name' => [],
        'controller' => [],
        'action' => [],
        'show' => ['view' => 'Boolean'],
        'type' => ['view' => 'Enum'],
        'priority' => [],
    ];

    protected function initialize()
    {
    }

    /**
     * 修改前字段验证
     *
     * @param  array
     * @return mixed
     */
    protected function updateable($id, array & $fields)
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
    public function deleteable($id)
    {
        // 判断是否是系统菜单，如果是则不允许删除
        if ($this->model()->isSystem($id)) {
            return 'Can\'t delete the system menu!';
        }
    }

    public function actionDetail1(Request $req, Response $resp, array & $params)
    {
        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $model = $this->model();

        $model->id = $id;

        $row = $model->find();

        $content = $this->admin()->content();
        $content->header(trans('菜单'));
        $content->description(trans('菜单编辑'));

        $content->row(function (Row $row) {
            $row->column(12, $this->form($row)->render());
        });

        return $content->render();
    }

    protected function iconHelp()
    {
        $url = Admin::url('PublicEntrance')->action('font-awesome');

        $label = trans_with_global('fontawesome icon CSS');

        return "<a onclick=\"open_tab('f-a-h', '{$url}', '$label')\">$label</a>";
    }

    /**
     * 编辑界面表单定义
     *
     * @param Form $form
     */
    protected function form(Form $form, Content $content)
    {
        $form->selectTree('parent_id')->options(resolve('acl-menu')->all())->defaultOption(0, '顶级分类');
        $form->text('name')->rules('required');
        $form->text('icon')->help($this->iconHelp());
        $form->text('controller');
        $form->text('action');
        $form->select('show')->options([1, 0])->default(1);
        $form->select('priority')->options(range(0, 30))->help('值越小排序越靠前');
    }

    /**
     * 网格定义
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid, Content $content)
    {
        $grid->rows(resolve('acl-menu')->all());
        $grid->disablePagination();
    }

    /**
     * 表格定义
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->useTree('subs');
    }


}
