<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Cards\Cards;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Auth\Ability;
use Lxh\Exceptions\FindModelException;
use Lxh\Exceptions\Forbidden;
use Lxh\Exceptions\InsertModelException;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Http\Uploads\Upload;
use Lxh\Http\VertifyCsrfToken;
use Lxh\Mvc\Controller as Base;

class Controller extends Base
{
    /**
     * id字段名称
     *
     * @var string
     */
    protected $idName = 'id';

    /**
     * 编辑页面id
     *
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
     * 文件上传字段
     *
     * @var array
     */
    protected $uploads = [];

    /**
     * 是否使用回收站
     *
     * @var bool
     */
    protected $trash = false;

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 使用防御csrf攻击中间件
        $this->middleware(VertifyCsrfToken::class);
    }

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

        if ($this->trash) {
            $grid->allowTrash();
        }

        // 权限设置
        $this->gridPermit($grid);

        if ($this->filter) {
            // 构建搜索界面
            $filter = new Filter();

            // 开启弹窗模式
            if ($this->filter === 'modal') {
                $filter->useModal();
            }

            // 自定义filter
            $this->filter($filter);

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

        if ($this->trash) {
            // 回收站入口
            if ($auth->can(__CONTROLLER__ . '.' . Ability::TRASH)) {
                $grid->allowTrashEntry();
            }

            // 还原
            if ($auth->can(__CONTROLLER__ . '.' . Ability::RESTORE)) {
                $grid->allowRestore();
            }

            // 彻底删除
            if ($auth->can(__CONTROLLER__ . '.' . Ability::DELETE_PERMANENTLY)) {
                $grid->allowDeletePermanently();
            }

            // 批量还原
            if ($auth->can(__CONTROLLER__ . '.' . Ability::BATCH_RESTORE)) {
                $grid->allowBatchRestore();
            }

            // 批量永久删除
            if ($auth->can(__CONTROLLER__ . '.' . Ability::BATCH_DELETE_PERMANENTLY)) {
                $grid->allowBatchDeletePermanently();
            }
        }
    }

    /**
     * Grid初始化之前触发
     *
     * @param Content $content
     */
    protected function beforeGridRowResolved(Content $content)
    {
    }

    /**
     * Grid初始化之后
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
            return $this->message(trans_with_global('Missing id.'), false);
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
        $isTrash = I(Grid::$trashKey);

        // 判断是否有权限访问
        if ($this->trash) {
            if ($isTrash) {
                // 如果开启了回收站功能，delete权限既是加入回收站权限
                if (! auth()->deleteable()) {
                    throw new Forbidden();
                }
            } else {
                if (! auth()->can(__CONTROLLER__ . '.' . Ability::DELETE_PERMANENTLY)) {
                    throw new Forbidden();
                }
            }
        } else {
            if (! auth()->deleteable()) {
                throw new Forbidden();
            }
        }

        if (empty($params['id'])) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($msg = $this->deleteFilter($params['id'])) {
            return $this->error($msg);
        }

        $model = $this->model();

        $model->setId($params['id']);

        if ($isTrash) {
            try {
                return $model->toTrash() ? $this->success() : $this->failed();

            } catch (FindModelException $e) {
                return $this->failed(trans('Target data does not exist.'));

            } catch (InsertModelException $e) {
                return $this->failed(trans($e->getMessage()));

            }
        }

        return $model->delete($this->trash) ? $this->success() : $this->failed();
    }

    /**
     * 批量还原接口
     *
     * @param array $params
     * @return array
     * @throws Forbidden
     */
    public function actionRestore(array $params)
    {
        // 判断是否有权限访问
        if (! auth()->can(__CONTROLLER__ . '.' . Ability::RESTORE)) {
            throw new Forbidden();
        }

        $ids = explode(',', I('ids'));

        if (empty($ids)) {
            return $this->error(trans_with_global('Missing id.'));
        }

        try {
            return $this->model()->restore($ids) ? $this->success() : $this->failed();

        } catch (FindModelException $e) {
            return $this->failed(trans('Target data does not exist.'));

        } catch (InsertModelException $e) {
            return $this->failed(trans($e->getMessage()));

        }

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

        if ($this->uploads) {
            $this->upload($input);
        }

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
        $input = &$_POST;

        if (! $input || !is_array($input)) {
            return $this->error();
        }

        if ($this->uploads) {
            $this->upload($input);
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
     * 文件上传
     *
     * @param array $input
     */
    protected function upload(array &$input)
    {
        foreach ($this->uploads as $column => $type) {
            $directory = '';
            switch ($type) {
                case 'image':
                    $directory = $this->getImageUploadDirectory();
                    break;
                case 'file':
                    $directory = $this->getFileUploadDirectory();
                    break;
            }

            $file = $this->request->getUploadedFile($column);

            // 删除原文件
            $originColumn = getvalue($input, $column.'-origin');
            $origin = $directory . '/' . $originColumn;

            if ($file) {
                $upload = new Upload($file, $directory);

                if ($upload->handle()) {
                    $input[$column] = $upload->getFormatTarget();

                    if ($originColumn && is_readable($origin)) {
                        unlink($origin);
                    }
                }
            } else {
                if ($originColumn && is_readable($origin)) {
                    unlink($origin);
                }
            }
            unset($input[$column.'-origin']);
        }
    }

    /**
     * @param $id
     * @param array $data
     */
    protected function updateFilter($id, array &$input)
    {
    }

    /**
     * 批量删除接口
     *
     * @return string
     */
    public function actionBatchDelete()
    {
        $isTrash = I(Grid::$trashKey);

        // 判断是否有权限访问
        if ($this->trash) {
            if ($isTrash) {
                // 如果开启了回收站功能，delete权限既是加入回收站权限
                if (! auth()->batchDeleteable()) {
                    throw new Forbidden();
                }
            } else {
                if (! auth()->can(__CONTROLLER__ . '.' . Ability::BATCH_DELETE_PERMANENTLY)) {
                    throw new Forbidden();
                }
            }
        } else {
            if (! auth()->batchDeleteable()) {
                throw new Forbidden();
            }
        }

        $ids = explode(',', I('ids'));

        if (empty($ids)) {
            return $this->error(trans_with_global('Missing id.'));
        }

        if ($isTrash) {
            try {
                return $this->model()->batchToTrash($ids) ? $this->success() : $this->failed();

            } catch (FindModelException $e) {
                return $this->failed(trans('Target data does not exist.'));

            } catch (InsertModelException $e) {
                return $this->failed(trans($e->getMessage()));

            }
        }

        return $this->model()->batchDelete($ids, $this->trash) ? $this->success() : $this->failed();
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getImageUploadDirectory()
    {
        return config('admin.upload.directory.image', '@lxh/resource/uploads/images');
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getFileUploadDirectory()
    {
        return config('admin.upload.directory.file', '@lxh/resource/uploads/files');
    }

}
