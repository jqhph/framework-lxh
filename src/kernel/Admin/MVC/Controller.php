<?php

namespace Lxh\Admin\MVC;

use Lxh\Admin\Admin;
use Lxh\Admin\Filter;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\MVC\Controller as Base;
use Lxh\Status;

class Controller extends Base
{
    /**
     * 网格字段配置
     *
     * @var array
     */
    protected $grid = [];

    /**
     * 是否使用过滤器
     *
     * @var bool
     */
    protected $filter = false;

    /**
     * 列表界面
     *
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionList(Request $req, Response $resp, array &$params)
    {
        $content = $this->admin()->content();

        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        if ($this->filter) {
            // 构建搜索界面
            $filter = $content->filter();

            // 自定义filter
            $this->filter($filter);
        }

        // 构建网格报表
        $grid = $content->grid($this->grid);

        // 自定义grid
        $this->grid($grid);

        if ($this->filter) {
            // 添加过滤器，过滤器会根据搜索表单内容构建Sql where过滤语句
            // 当然，你也可以在Model中重新定义where语句内容
            $grid->filter($filter);
        }

        // 自定义表格
        // 可以自定义行、列、表头的内容和样式等，也可以追加列
        $this->table($grid->table());

        // 渲染模板
        return $content->render();
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
    public function actionCreate(Request $req, Response $resp, array &$params)
    {
        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' form'));

        $box = $content->form(function (Form $form) {
            $form->useEditScript();
            
            $this->form($form);
        });

        $box->title(trans('Create ' . __CONTROLLER__));

        $this->formBox($box);

        return $content->render();
    }

    /**
     * 新增界面
     *
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionDetail(Request $req, Response $resp, array &$params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' form'));

        $box = $content->form(function (Form $form) use ($id) {
            // 设置id，用于查询当前行数据
            $form->setId($id);
            // 自定义form表单
            $this->form($form);
        });

        $box->title(trans('Edit ' . __CONTROLLER__));

        $this->formBox($box);

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
    protected function form(Form $form)
    {
    }

    /**
     * 删除数据接口
     *
     * @return array
     */
    public function actionDelete(Request $req, Response $resp, array &$params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($msg = $this->deleteable($params['id'])) {
            return $this->error($msg);
        }

        $model = $this->model();

        $model->id = $params['id'];

        return $model->delete() ? $this->success() : $this->failed();
    }

    /**
     * @param $id
     */
    protected function deleteable($id)
    {
    }

    /**
     * 新增数据接口
     *
     * @return array
     */
    public function actionAdd(Request $req, Response $resp, array &$params)
    {
        // 判断是否有权限访问
        if (! acl()->accessCreate()) {
            throw new Forbidden();
        }

        if (! $_POST) {
            return $this->error();
        }

        if ($rules = $this->rules()) {
            $validator = $this->validator();
            $validator->fill($_POST);
        }

        // 验证表单数据
        if ($msg = $this->createable($_POST)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if ($rules && ! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->model();

        // 注入表单数据
        $model->fill($_POST);

        return $model->add() ? $this->success() : $this->failed();
    }

    /**
     * @param array $data
     */
    protected function createable(array &$data)
    {

    }

    /**
     * 表单字段验证规则
     *
     * @return void|array
     */
    protected function rules()
    {
    }


    /**
     * 修改数据接口
     *
     * @return array
     */
    public function actionUpdate(Request $req, Response $resp, array &$params)
    {
        // 判断是否有权限访问
        if (! acl()->accessUpdate()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        // 获取表单数据
        $data = json_decode(file_get_contents('php://input'), true);

        if (! $data) {
            return $this->error();
        }

        if ($rules = $this->rules()) {
            $validator = $this->validator();

            $validator->fill($data);
            $validator->rules($rules);
        }

        // 验证表单数据
        if ($msg = $this->updateable($params['id'], $data)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if ($rules && ! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->model();

        // 注入表单数据
        $model->fill($data);

        return $model->save() ? $this->success() : $this->failed();
    }

    /**
     * @param $id
     * @param array $data
     */
    protected function updateable($id, array &$data)
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
        if (! acl()->access()) {
            throw new Forbidden();
        }

        $ids = explode(',', I('ids'));

        if (empty($ids)) {
            return $this->error(trans_with_global('Missing id.'));
        }

        return $this->model()->batchDelete($ids) ? $this->success() : $this->failed();
    }

    /**
     * 获取字段验证处理器
     * 用法清请参考：https://github.com/vlucas/valitron
     *
     *  $v->fill(['name' => '张三', 'email' => 'jqh@163.com'])
    ->rule('required', array('name', 'email'))
    ->rule('email', 'email');

    if ($v->validate()) {
    echo "Yay! We're all good!<br>";
    } else {
    // Errors
    debug($v->errors());
    }
     *
     * @return Validator
     */
    protected function validator()
    {
        return $this->container['validator'];
    }

    /**
     * 返回成功信息
     *
     * @return array
     */
    protected function success($msg = 'Success', array $options = [])
    {
        return $this->message($msg, Status::SUCCESS, $options);
    }

    /**
     * 返回失败信息
     *
     * @return array
     */
    protected function failed($msg = 'Failed', array $options = [])
    {
        return $this->message($msg, Status::FAILED, $options);
    }

    /**
     * 返回错误信息
     *
     * @return array
     */
    protected function error($msg = 'Invalid arguments', $status = Status::INVALID_ARGUMENTS)
    {
        return $this->message($msg, $status);
    }

    /**
     * 返回数据到web
     *
     * @return array
     */
    protected function message($msg, $status, array $options = [])
    {
        return (['status' => & $status, 'msg' => & $msg] + $options);
    }
}
