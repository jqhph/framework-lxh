<?php
/**
 * 公共控制器
 *
 * @author Jqh
 * @date   2017/7/21 10:37
 */

namespace Lxh\Kernel\Controller;

use Lxh\Exceptions\Forbidden;
use Lxh\MVC\Controller as LxhController;
use Lxh\Status;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Record extends LxhController
{
    // 是否加载自身js，否则加载公共js
    protected $loadJs = false;

    protected $maxSize = 20;

    protected $detailTemplate = 'component.detail.detail';

    protected $createTemplate = 'component.detail.detail';

    protected $listTemplate = 'component.list.list';

    protected $btns = [
        'create' => 'Create'
    ];

    /**
     * list页
     *
     * @return string
     */
    public function actionList(array $params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        $pages = pages();

        // 生成where条件数组
        $wheres = $this->makeListWhereContent($_REQUEST);

        $model = $this->model();

        // 获取记录总条数
        $total = $model->count($wheres);

        $pageString = $pages->make($total, $this->maxSize);

        // 生成分页字符串后获取当前分页（做过安全判断）
        $currentPage = $pages->current();

        $list = [];

        if ($total) {
            $list = $model->findList($wheres, $this->makeOrderContent($_REQUEST), ($currentPage - 1) * $this->maxSize, $this->maxSize);
        }

        // 获取列表table标题信息
        $titles = $this->makeListItems();

        return $this->render(
            $this->listTemplate,
            [
                'list' => & $list,
                'searchItems' => $this->makeSearchItems(),
                'items' => & $titles,
                'pages' => & $pageString,
                'btns' => & $this->btns
            ],
            true
        );
    }

    /**
     * 获取list页table标题信息
     *
     * @return array
     */
    protected function makeListItems()
    {
        return [];
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
     * 创建记录界面
     */
    public function actionCreate(array $params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        $currentTitle = 'Create ' . __CONTROLLER__;

        $this->share('navTitle', $currentTitle);

        return $this->render(
            $this->createTemplate,
            [
                'items' => $this->makeDetailItems(),
                'loadJs' => $this->loadJs,
                'validatorRules' => $this->makeClientValidatorRules()
            ]
            , true
        );
    }

    /**
     * 批量删除接口
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
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function makeDetailItems($id = null)
    {
        return [];
    }

    /**
     * 修改记录界面
     *
     * @param Request $req
     * @param Response $resp
     * @param $params
     * @return string
     * @throws Forbidden
     */
    public function actionDetail(array $params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $model = $this->model();

        $model->id = $id;

        $row = $model->find();

        $currentTitle = 'Modify ' . __CONTROLLER__;

        $this->share('navTitle', $currentTitle);

        return $this->render(
            $this->detailTemplate,
            [
                'row' => & $row,
                'items' => $this->makeDetailItems($id),
                'loadJs' => $this->loadJs,
                'validatorRules' => $this->makeClientValidatorRules()
            ],
            true
        );
    }

    // 前端字段验证规则
    protected function makeClientValidatorRules()
    {
        return [];
    }

    /**
     * 生成where条件内容
     *
     * @param array $options
     * @return array
     */
    protected function makeListWhereContent(array & $options)
    {
        return ['deleted' => 0];
    }

    /**
     * 生成order by字符串
     *
     * @param array $options
     * @return string
     */
    protected function makeOrderContent(array & $options)
    {
        if (! empty($options['sort'])) {
            $desc = I('desc', true);
            return "`{$options['sort']}`" . ($desc ? ' DESC ' : ' ASC ');
        }

        return 'id DESC';
    }

    /**
     * 删除数据接口
     *
     * @return array
     */
    public function actionDelete(array $params)
    {
        // 判断是否有权限访问
        if (! acl()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($msg = $this->deleteValidate($params['id'])) {
            return $this->error($msg);
        }

        $model = $this->model();

        $model->id = $params['id'];

        return $model->delete() ? $this->success() : $this->failed();
    }

    // 删除操作验证操作方法
    protected function deleteValidate($id)
    {

    }

    /**
     * 新增数据接口
     *
     * @return array
     */
    public function actionAdd(array $params)
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
        if ($msg = $this->updateValidate(null, $_POST)) {
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
     * 修改数据接口
     *
     * @return array
     */
    public function actionUpdate(Request $req, Response $resp, & $params)
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
        if ($msg = $this->updateValidate($params['id'], $data)) {
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
     * 修改前字段验证
     *
     * @param  array
     * @return array
     */
    protected function updateValidate($id, array & $fields)
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
     * 返回数据到web
     *
     * @return array
     */
    protected function toMsg($msg, $status, array $options = [])
    {
        return (['status' => & $status, 'msg' => & $msg] + $options);
    }

    /**
     * 返回成功信息
     *
     * @return array
     */
    protected function success($msg = 'Success', array $options = [])
    {
        return $this->toMsg($msg, Status::SUCCESS, $options);
    }

    /**
     * 返回失败信息
     *
     * @return array
     */
    protected function failed($msg = 'Failed', array $options = [])
    {
        return $this->toMsg($msg, Status::FAILED, $options);
    }

    /**
     * 返回错误信息
     *
     * @return array
     */
    protected function error($msg = 'Invalid arguments', $status = Status::INVALID_ARGUMENTS)
    {
        return $this->toMsg($msg, $status);
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
        return $this->container->make('validator');
    }
}