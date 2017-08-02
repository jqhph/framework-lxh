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
    protected $maxSize = 20;

    /**
     * list页
     *
     * @return string
     */
    public function actionList()
    {
        // 判断是否有权限访问
        if (! permit()->access()) {
            throw new Forbidden();
        }

        $page = I('page', 1);

        if ($page < 1) {
            $page = 1;
        }

        $wheres = array_merge($this->makeWhereContent($_REQUEST), ['deleted' => 0]);

        $model = $this->getModel();

        // 获取记录总条数
        $total = $model->count($wheres);

        $totalPage = ceil($total / $this->maxSize);

        if ($page > $totalPage) {
            $page = $totalPage;
        }

        $list = [];

        if ($total) {
            $list = $model->records($wheres, $page, $this->maxSize, $this->makeOrderContent($_REQUEST));
        }

        $pages = pages($total, $page, $this->maxSize);

        return fetch_complete_view('List', ['list' => & $list, 'searchItems' => $this->getSearchItems(), 'titles' => $this->getListTableTitles(), 'pages' => & $pages]);
    }

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
     * 创建记录界面
     */
    public function actionCreate(Request $req, Response $resp, & $params)
    {
        // 判断是否有权限访问
        if (! permit()->access()) {
            throw new Forbidden();
        }

        $currentTitle = 'Create ' . __CONTROLLER__;

        assign('navTitle', $currentTitle);

        return fetch_complete_view('Detail', ['detailFields' => $this->getDetailFields()]);
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function getDetailFields()
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
    public function actionDetail(Request $req, Response $resp, & $params)
    {
        // 判断是否有权限访问
        if (! permit()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            throw new Forbidden();
        }
        $id = $params['id'];

        $model = $this->getModel();

        $model->id = $id;

        $row = $model->find();

        $currentTitle = 'Modify ' . __CONTROLLER__;

        assign('navTitle', $currentTitle);

        return fetch_complete_view(__ACTION__, [
            'row' => & $row, 'detailFields' => $this->getDetailFields()
        ]);
    }


    /**
     * 生成where条件内容
     *
     * @param array $options
     * @return array
     */
    protected function makeWhereContent(array & $options)
    {
        return [];
    }

    /**
     * 生成order by字符串
     *
     * @param array $options
     * @return string
     */
    protected function makeOrderContent(array & $options)
    {
        return 'id Desc';
    }

    /**
     * 删除数据接口
     *
     * @return array
     */
    public function actionDelete(Request $req, Response $resp, & $params)
    {
        // 判断是否有权限访问
        if (! permit()->access()) {
            throw new Forbidden();
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($msg = $this->deleteValidate($params['id'])) {
            return $this->error($msg);
        }

        $model = $this->getModel();

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
    public function actionAdd(Request $req, Response $resp, & $params)
    {
        // 判断是否有权限访问
        if (! permit()->accessCreate()) {
            throw new Forbidden();
        }

        if (! $_POST) {
            return $this->error();
        }

        $validator = $this->validator();

        $validator->fill($_POST);

        // 验证表单数据
        if ($msg = $this->updateValidate(null, $_POST, $validator)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if (! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->getModel();

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
        if (! permit()->accessUpdate()) {
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

        $validator = $this->validator();

        $validator->fill($data);

        // 验证表单数据
        if ($msg = $this->updateValidate($params['id'], $data, $validator)) {
            return $this->error($msg);
        }

        // 验证并获取结果
        if (! $validator->validate()) {
            return $this->error($validator->errors());
        }

        // 获取模型
        $model = $this->getModel();

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
    protected function updateValidate($id, array & $fields, Validator $validator)
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