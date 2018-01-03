<?php

namespace Lxh\Admin\MVC;

use Lxh\Admin\Filter;
use Lxh\Admin\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Exceptions\Forbidden;
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
     * @param Request $req
     * @param Response $resp
     * @param array $params
     */
    public function actionList(Request $req, Response $resp, array &$params)
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
     * Form初始化方法
     *
     * @param Form $form
     */
    protected function form(Form $form)
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

    protected function beforeBatchDelete(array &$ids)
    {
    }

    protected function afterBatchDelete(array &$ids)
    {
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
