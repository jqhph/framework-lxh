<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
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
    public function actionCreate1(Request $req, Response $resp, & $params)
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
    public function actionDetail1(Request $req, Response $resp, array & $params)
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

    public function actionDetail(Request $req, Response $resp, array & $params)
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

    /**
     * 新增操作界面
     *
     * @return string
     */
    public function actionCreate(Request $req, Response $resp, & $params)
    {
        $content = $this->admin()->content();
        $content->header(trans('Menu'));
        $content->description(trans('Menu form'));

        $box = $content->form(function (Form $form) {
            $form->action(Url::makeAction('create'));

            $form->selectTree('parent_id')->options(resolve('acl-menu')->all())->defaultOption(0, '顶级分类');
            $form->text('title')->rules('required');
            $form->text('icon')->help($this->iconHelp());
            $form->text('name')->rules('required');
            $form->text('controller');
            $form->text('action');
            $form->select('show')->options([1, 0])->default(1);
            $form->select('priority')->options(range(0, 30))->help('值越小排序越靠前');

            $form->useEditScript();
        });

        $box->title(trans('Create Menu'));

        return $content->render();
    }

    protected function iconHelp()
    {
        $url = Url::makeAction('font-awesome', 'public-entrance');
        $label = trans_with_global('fontawesome icon CSS');

        return "<a onclick=\"open_tab('f-a-h', '{$url}', '$label')\">$label</a>";
    }

    public function actionList(Request $req, Response $resp, array & $params)
    {
        $content = $this->admin()->content();
        $content->header(trans('Menu'));
        $content->description(trans('Menu list'));

        $grid = $content->grid([
            'id' => ['show' => 0, 'sortable' => 1, 'desc' => 1],
            'icon' => ['view' => 'Icon'],
            'name' => [],
            'controller' => [],
            'action' => [],
            'show' => ['view' => 'Boolean'],
            'type' => ['view' => 'Enum'],
            'priority' => [],
        ]);

        $rows = resolve('acl-menu')->all();

        $grid->rows($rows)->disablePagination()->table()->useTree('subs');

        return $content->render();
    }


}
