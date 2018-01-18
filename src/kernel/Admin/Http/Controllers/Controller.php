<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Box;
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
     * 网格字段配置
     *
     * @var array
     */
    protected $grid = [];

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
        $content = $this->admin()->content();

        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        $evt = $this->getLowerCaseDashName();
        $list = "admin.$evt.list";
        // 列表页创建grid前
        fire($list . '.grid.before', [$content]);

        if ($this->filter) {
            // 构建搜索界面
            $filter = $content->filter();

            // 自定义filter
            $this->filter($filter);
            // 开启弹窗模式
            $this->filter == 'modal' && $filter->useModal();

            // 列表页创建filter后
            fire($list . '.filter', [$filter]);
        }

        $this->beforeGridCreate($content);

        // 构建网格报表
        $grid = $content->grid($this->grid);

        // 自定义grid
        $this->grid($grid, $content);

        $this->gridPermit($grid);

        if ($this->filter) {
            // 添加过滤器，过滤器会根据搜索表单内容构建Sql where过滤语句
            // 当然，你也可以在Model中重新定义where语句内容
            $grid->filter($filter);
        }

        // 自定义表格
        // 可以自定义行、列、表头的内容和样式等，也可以追加列
        $this->table($table = $grid->table());

        // 列表页创建grid后
        fire($list . '.grid.after', [$grid, $table]);

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
     * @param Content $content
     */
    protected function beforeGridCreate(Content $content)
    {
    }

    /**
     * Grid初始化方法
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid, Content $content)
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
        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' form'));

        $evt = $this->getLowerCaseDashName();
        $evt = "admin.$evt.create";

        // 新建页创建from前
        fire($evt . '.form.before', [$content]);

        $this->beforeFormCreate($content);

        $form = '';
        $box = $content->form(function (Form $f) use ($content, $form) {
            $form = $f;
            $this->form($f, $content);
        });

        $box->title(trans('Create ' . __CONTROLLER__));

        $this->formBox($box);

        // 列表页创建form后
        fire($evt . '.form.after', [$content, $form, $box]);

        return $content->render();
    }

    protected function beforeFormCreate(Content $content)
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

        $evt = $this->getLowerCaseDashName();
        $evt = "admin.$evt.detail";

        // 详情页创建from前
        fire($evt . '.form.before', [$this->id, $content]);

        $form = '';
        $box = $content->form(function (Form $f) use ($id, $content, $form) {
            $form = $f;
            // 设置id，用于查询当前行数据
            $form->setId($id);
            // 自定义form表单
            $this->form($form, $content);

            if (! auth()->updateable()) {
                $form->disableSubmit();
            }
        });

        $box->title(trans('Edit ' . __CONTROLLER__));

        $this->formBox($box);

        // 详情页创建from前
        fire($evt . '.form.after', [$this->id, $content, $form, $box]);

        return $content->render();
    }

    /**
     * @param Box $box
     */
    protected function formBox(Box $box)
    {
    }

    /**
     * Form初始化方法
     *
     * @param Form $form
     */
    protected function form(Form $form, Content $content)
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
            $validator = $this->validator();
            $validator->fill($input);
            $validator->rules($rules);
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
        $data = json_decode(file_get_contents('php://input'), true);

        if (! $data || !is_array($data)) {
            return $this->error();
        }

        // 过滤用户输入数据
        $this->inputFilter($data);

        if ($rules = $this->rules()) {
            $validator = $this->validator();

            $validator->fill($data);
            $validator->rules($rules);
        }

        // 验证表单数据
        if ($msg = $this->updateFilter($params['id'], $data)) {
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
        $model->attach($data);

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
