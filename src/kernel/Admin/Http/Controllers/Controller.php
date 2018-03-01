<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Cards\Cards;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Util;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\MVC\Controller as Base;
use Lxh\Status;

class Controller extends Base
{
    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var int
     */
    protected $id;

    /**
     *
     * @var Grid
     */
    protected $grid;

    /**
     * @var Form
     */
    protected $form;

    /**
     * 网格宽度
     *
     * @var int
     */
    protected $gridWidth = 12;

    /**
     * 创建页面表单宽度
     *
     * @var int
     */
    protected $createFormWidth = 12;

    /**
     * 编辑页面表单宽度
     *
     * @var int
     */
    protected $detailFormWidth = 12;

    /**
     * 是否使用过滤器
     *
     * @var bool|string
     */
    protected $filter = false;

    /**
     * 列表界面
     *
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionList(array $params)
    {
        if (! auth()->readable()) {
            throw new Forbidden();
        }
        
        $content = $this->admin()
            ->content()
            ->header(trans(__CONTROLLER__))
            ->description(trans(__CONTROLLER__ . ' list'));

        $evt = $this->getLowerCaseDashName();
        $list = "admin.$evt.list";
        // 列表页创建grid前
        fire($list . '.grid.before', [$content]);

        // 构建网格报表
        $this->grid = $grid = new Grid();

        // 网格行创建前
        $this->beforeGridRowResolved($content);

        // 权限设置
        $this->gridPermit($grid);

        if ($this->filter) {
            // 构建搜索界面
            $filter = new Filter();

            // 自定义filter
            $this->filter($filter);
            // 开启弹窗模式
            if ($this->filter === 'modal') {
                $filter->useModal();
            }

            // 列表页创建filter后
            fire($list . '.filter', [$filter]);

            // 添加过滤器，过滤器会根据搜索表单内容构建Sql where过滤语句
            // 当然，你也可以在Model中重新定义where语句内容
            $grid->filter($filter);
        }

        // 自定义grid
        $this->grid($grid);

        if ($this->filter) {
            if (!$filter->allowedInTable()) {
                $content->row($filter);
            }
        }

        // 网格
        $content->row(function (Row $row) use ($content, $grid) {
            $this->beforeGridColumnResolved($row);

            // 添加网格配置
//            $grid->headers($this->grid);

            // 添加列
            $column = $row->column($this->gridWidth, $grid);

            $this->afterGridColumnResolved($row);
        });

        // 网格行穿件后
        $this->afterGridRowResolved($content);

        if ($grid->getLayout() == 'card') {
            $grid->card()->resolving([$this, 'card']);
        } else {
            // 自定义表格
            // 可以自定义行、列、表头的内容和样式等，也可以追加列
            $this->table($table = $grid->table());
        }

        // 列表页创建grid后
        fire($list . '.grid.after', [$grid]);

        // 渲染模板
        return $content->render();
    }

    /**
     * 权限判断
     *
     * @param Grid $grid
     */
    protected function gridPermit(Grid $grid)
    {
        $auth = auth();

        // 检查是否有创建权限
        if (!$auth->createable()) {
            $grid->disableCreate();
        }

        // 检查是否有详情页查看权限
        if (!$auth->detailable()) {
            $grid->disableEdit();
        }

        // 检查是否有删除权限
        if (!$auth->deleteable()) {
            $grid->disableDelete();
        }

        // 检查是否允许批量删除
        if ($auth->batchDeleteable()) {
            $grid->allowBatchDelete();
        }
    }

    /**
     * 网格行创建前
     *
     * @param Content $content
     */
    protected function beforeGridRowResolved(Content $content)
    {
    }

    /**
     * 网格行创建后
     *
     * @param Content $content
     */
    protected function afterGridRowResolved(Content $content)
    {
    }

    /**
     * 网格列创建前
     *
     * @param Row $row
     */
    protected function beforeGridColumnResolved(Row $row)
    {
    }

    /**
     * 网格列创建后
     *
     * @param Row $row
     */
    protected function afterGridColumnResolved(Row $row)
    {
    }

    /**
     * Grid初始化方法
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid)
    {
    }

    /**
     * 瀑布流卡片布局渲染方法
     *
     * @param Cards $cards
     */
    public function card(Cards $cards)
    {
    }

    /**
     * Table初始化方法
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
    }

    /**
     * 过滤器初始化方法
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
    }

    /**
     * 新增界面
     *
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionCreate(array $params)
    {
        if (! auth()->createable()) {
            throw new Forbidden();
        }

        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' form'));

        $evt = "admin.{$this->getLowerCaseDashName()}.create";

        // 新建页创建from前
        fire($evt . '.form.before', [$content]);

        $this->form = $form = new Form();
        $form->setContent($content);

        // 表单行创建前
        $this->beforeFormRowResolved($content);

        $box = new Card(trans('Create ' . __CONTROLLER__), $form);
        $content->row(function (Row $row) use ($content, $form, $box) {
            // 表单列创建前
            $this->beforeFormColumnResolved($row);

            // 自定义form表单
            $this->form($form);

            $box->content($form);

            if (! auth()->createable()) {
                $form->disableSubmit();
            }

            $column = $row->column($this->createFormWidth, $box->backable());

            // 表单列创建后
            $this->afterFormColumnResolved($row);
        });

        $this->afterFormRowResolved($content, $box);

        // 列表页创建form后
        fire($evt . '.form.after', [$content, $form, $box]);

        return $content->render();
    }

    /**
     * 修改单个字段
     *
     * @param array $params
     * @return array
     */
    public function actionUpdateField(array $params)
    {
        // 判断是否有权限访问
        if (! auth()->updateable()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->message('INVALID ARGUMENTS', false);
        }
        $name = I('name');
        $value = I('value');

        // 过滤
        $this->updateFieldInputFilter($value, $name);

        // 过滤
        $value = apply_filters($this->getLowerCaseDashName() . '.update.field', $value, $name);

        if (empty($name)) {
            return $this->message('INVALID ARGUMENTS', false);
        }

        $model = $this->model();
        $model->setId($params['id']);

        $model->$name = $value;

        if ($model->save()) {
            return $this->message('Update succeeded', true);
        }
        return $this->message('FIELED', false);
    }

    /**
     * @param $value
     * @param $name
     */
    protected function updateFieldInputFilter(&$value, $name)
    {
    }

    /**
     * 新增界面
     *
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionDetail(array $params)
    {
        if (! auth()->detailable()) {
            throw new Forbidden();
        }

        // 判断是否有权限访问
        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $this->id = $id = $params['id'];

        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' form'));

        $evt = "admin.{$this->getLowerCaseDashName()}.detail";

        // 详情页创建from前
        fire($evt . '.form.before', [$this->id, $content]);

        $this->form = $form = new Form();
        $form->setId($id);
        $form->setContent($content);

        // 表单行创建前
        $this->beforeFormRowResolved($content);

        $box = new Card(trans('Edit ' . __CONTROLLER__), $form);
        $content->row(function (Row $row) use ($id, $content, $form, $box) {
            // 表单列创建前
            $this->beforeFormColumnResolved($row);

            // 自定义form表单
            $this->form($form);

            $box->content($form);

            if (! auth()->updateable()) {
                $form->disableSubmit();
            }

            $column = $row->column($this->detailFormWidth, $box->backable());

            // 表单列创建后
            $this->afterFormColumnResolved($row);
        });

        // 表单行创建后
        $this->afterFormRowResolved($content, $box);

        // 详情页创建from前
        fire($evt . '.form.after', [$this->id, $content, $form, $box]);

        return $content->render();
    }

    /**
     * 表单行创建前
     *
     * @param Content $content
     */
    protected function beforeFormRowResolved(Content $content)
    {
    }

    /**
     * 表单列创建前
     *
     * @param Row $row
     */
    protected function beforeFormColumnResolved(Row $row)
    {
    }

    /**
     * 表单列创建后
     *
     * @param Row $row
     */
    protected function afterFormColumnResolved(Row $row)
    {
    }

    /**
     * @param Content $content
     * @param Card $card
     */
    protected function afterFormRowResolved(Content $content, Card $card)
    {
    }

    /**
     * Form初始化方法
     *
     * @param Form $form
     */
    protected function form(Form $form)
    {
    }

    /**
     * 删除数据接口
     *
     * @return array
     */
    public function actionDelete(array $params)
    {
        // 判断是否有权限访问
        if (! auth()->deleteable()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($msg = $this->deleteFilter($params['id'])) {
            return $this->error($msg);
        }

        $model = $this->model();

        $model->setId($params['id']);

        return $model->delete() ? $this->success() : $this->failed();
    }

    /**
     * @param $id
     */
    protected function deleteFilter($id)
    {
    }

    /**
     * 户输入表单数据过滤
     *
     * @param array $input
     */
    protected function inputFilter(array &$input)
    {
        unset($input['_token']);
    }

    /**
     * 新增数据接口
     *
     * @return array
     */
    public function actionAdd(array $params)
    {
        // 判断是否有权限访问
        if (! auth()->createable()) {
            throw new Forbidden();
        }

        if (! $_POST) {
            return $this->error();
        }
        $input = $_POST;

        $this->inputFilter($input);

        if ($rules = $this->rules()) {
            $validator = $this->validator($input, $rules);
        }

        // 验证表单数据
        if ($msg = $this->addFilter($input)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if ($rules && ! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->model();

        // 注入表单数据
        $model->attach($input);

        return $model->add() ? $this->success() : $this->failed();
    }

    /**
     * @param array $data
     */
    protected function addFilter(array &$input)
    {
    }

    /**
     * 表单字段验证规则
     *
     * @return array
     */
    protected function rules()
    {
    }


    /**
     * 修改数据接口
     *
     * @return array
     */
    public function actionUpdate(array $params)
    {
        // 判断是否有权限访问
        if (! auth()->updateable()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        $this->id = $params['id'];

        // 获取表单数据
        $input = json_decode(file_get_contents('php://input'), true);

        if (! $input || !is_array($input)) {
            return $this->error();
        }

        // 过滤用户输入数据
        $this->inputFilter($input);

        if ($rules = $this->rules()) {
            $validator = $this->validator($input, $rules);
        }

        // 验证表单数据
        if ($msg = $this->updateFilter($params['id'], $input)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if ($rules && ! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->model();

        // 设置id字段名称
        $model->setId($params['id']);

        // 注入表单数据
        $model->attach($input);

        return $model->save() ? $this->success() : $this->failed();
    }

    /**
     * @param $id
     * @param array $data
     */
    protected function updateFilter($id, array &$data)
    {
    }

    /**
     * 批量删除接口
     *
     * @return string
     */
    public function actionBatchDelete()
    {
        // 判断是否有权限访问
        if (! auth()->batchDeleteable()) {
            throw new Forbidden();
        }

        $ids = explode(',', I('ids'));

        if (empty($ids)) {
            return $this->error(trans_with_global('Missing id.'));
        }

        return $this->model()->batchDelete($ids) ? $this->success() : $this->failed();
    }

}
