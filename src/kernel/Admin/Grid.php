<?php

namespace Lxh\Admin;

use Lxh\Admin\Cards\Cards;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Grid\LayoutSwitcher;
use Lxh\Admin\Grid\TrashButtons;
use Lxh\Admin\Grid\RowActions;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Tools\Actions;
use Lxh\Admin\Tools\Tools;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Pages;
use Lxh\Contracts\Support\Renderable;
use Lxh\MVC\Model;
use Lxh\Http;

class Grid implements Renderable
{
    const LAYOUT_TABLE = 'table';
    const LAYOUT_CARD = 'card';

    /**
     * 数据模型
     *
     * @var Model
     */
    protected $model;

    /**
     * 表格对象
     *
     * @var Table
     */
    protected $table;

    /**
     * 瀑布流卡片对象
     *
     * @var Cards
     */
    protected $cards;

    /**
     * 数据
     *
     * @var array
     */
    protected $rows;

    /**
     * Mark if the grid is builded.
     *
     * @var bool
     */
    protected $builded = false;

    /**
     * The grid Filter.
     *
     * @var \Lxh\Admin\Filter
     */
    protected $filter;

    /**
     * Export driver.
     *
     * @var string
     */
    protected $exporter;

    /**
     * View for grid to render.
     *
     * @var string
     */
    protected $view = 'admin::grid';

    /**
     * Per-page options.
     *
     * @var array
     */
    public $perPages = [10, 15, 20, 25, 30, 40, 50, 80, 100, 200, 500];

    /**
     * Default items count per-page.
     *
     * @var int
     */
    public $perPage = 20;

    /**
     * @var string
     */
    protected $perPageKey = 'maxSize';

    /**
     * 左边工具栏
     *
     * @var Tools
     */
    protected $tools;

    /**
     * 动作按钮
     *
     * @var \Lxh\Admin\Tools\Actions
     */
    protected $actions;

    /**
     * 分页工具
     *
     * @var mixed
     */
    protected $paginator;

    /**
     * @var TrashButtons
     */
    protected $trashButtons;

    /**
     * 配置选项
     *
     * @var array
     */
    protected $options = [
        // 使用分页
        'usePagination'                 => true,
        // 使用过滤器
        'useFilter'                     => false,
        // 导出
        'useExporter'                   => true,
        // 使用行选择器
        'useRowSelector'                => true,
        // 编辑
        'allowEdit'                     => true,
        // 删除
        'allowDelete'                   => true,
        // 创建
        'allowCreate'                   => true,
        // 批量删除
        'allowBatchDelete'              => false,
        // 表格响应式工具
        'useRWD'                        => true,
        // 列表网格加载的js
        'indexScript'                   => '@lxh/js/public-index',
        // 使用pjax
        'pjax'                          => true,
        // 布局切换
        'useLayoutSwitcher'             => false,
        // 使用回收站
        'useTrash'                      => false,
        // 回收站入口
        'allowTrashEntry'               => false,
        // 从回收站还原
        'allowedRestore'                => false,
        // 永久删除
        'allowedDeletePermanently'      => false,
        // 批量永久删除
        'allowedBatchDeletePermanently' => false,
        // 批量还原
        'allowedBatchRestore'           => false,
        // 刷新按钮
        'allowedRefresh'                => true,
        'defaultOrderByString'          => '',
    ];

    /**
     * 分页html字符串
     *
     * @var string
     */
    protected $pageString = '';

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * 主键字段名
     *
     * @var string
     */
    protected $idName = 'id';

    /**
     * pjax请求参数名称
     *
     * @var string
     */
    public static $pjaxKey = Http\Request::PJAX;

    /**
     * view布局参数名称
     *
     * @var string
     */
    public static $viewKey = '_vw';

    /**
     * 是否是回收站页参数名
     *
     * @var string
     */
    public static $trashKey = '_trash';

    /**
     * 当前页面是否是回收站
     *
     * @var bool
     */
    protected $isTrash = false;

    /**
     * current url
     *
     * @var Http\Url
     */
    protected $url;

    /**
     * @var RowActions
     */
    protected $rowActions;

    /**
     * @var string
     */
    protected $layout = 'table';

    /**
     * @var \Closure
     */
    protected $orderByFilter;

    /**
     * Create a new grid instance.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array &$rows = [])
    {
        $this->rows    = &$rows;
        $this->tools   = new Tools();
        $this->url     = new Http\Url();
        $this->idName  = Admin::id();
        $this->isTrash = I(static::$trashKey);

        $this->setupPerPage();
        $this->setupLayoutForRequestParams();
    }

    /**
     * @return Http\Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 获取id键名
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->idName;
    }

    /**
     * 判断是否是回收站
     *
     * @return bool
     */
    public function isTrash()
    {
        return $this->isTrash;
    }

    /**
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'pjax-container';
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * 使用瀑布流卡片布局
     *
     * @return $this
     */
    public function useCard()
    {
        if (! empty(I(static::$viewKey))) {
            return $this;
        }
        $this->layout = static::LAYOUT_CARD;
        $this->disableResponsive();
        return $this;
    }

    /**
     * 使用表格布局
     *
     * @return $this
     */
    public function useTable()
    {
        if (! empty(I(static::$viewKey))) {
            return $this;
        }
        $this->layout = static::LAYOUT_TABLE;
        return $this;
    }

    /**
     * 使用布局切换按钮
     *
     * @return $this
     */
    public function useLayoutSwitcher()
    {
        $this->options['useLayoutSwitcher'] = true;
        return $this;
    }

    /**
     * 加载js脚本
     *
     * @param $script
     * @return $this
     */
    public function useGridScript($script)
    {
        $this->options['indexScript'] = &$script;

        return $this;
    }

    public function allowTrash()
    {
        $this->options['useTrash'] = true;

        return $this;
    }

    public function disableTrash()
    {
        $this->options['useTrash'] = false;

        return $this;
    }

    public function allowTrashEntry()
    {
        $this->options['allowTrashEntry'] = true;

        return $this;
    }

    public function disableTrashEntry()
    {
        $this->options['allowTrashEntry'] = false;

        return $this;
    }

    /**
     * 禁止使用公共js脚本
     *
     * @return $this
     */
    public function disableGridScript()
    {
        $this->options['indexScript'] = '';

        return $this;
    }

    public function allowRefresh()
    {
        $this->options['allowedRefresh'] = true;

        return $this;
    }

    public function disableRefresh()
    {
        $this->options['allowedRefresh'] = false;

        return $this;
    }


    /**
     * 设置动作按钮
     *
     * @return Actions
     */
    public function actions(\Closure $callback = null)
    {
        if (! $this->actions) {
            $this->actions = new Actions();
        }

        $callback && $callback($this->actions);

        return $this->actions;
    }

    /**
     * 设置表格行数据
     *
     * @param array $rows
     * @return static
     */
    public function rows(array $rows)
    {
        $this->rows = &$rows;
        return $this;
    }

    /**
     * 数组方式配置渲染table字段
     *
     * @param array $headers 支持参数说明
     * @return $this
     */
    public function headers(array $headers)
    {
        if ($this->layout == static::LAYOUT_CARD) {
            $this->card()->setFields($headers);
        } else {
            $this->table()->setHeaders($headers);
        }

        return $this;
    }

    /**
     * 初始化每页显示行数
     *
     */
    protected function setupPerPage()
    {
        $maxSize = I($this->perPageKey);

        if (!in_array($maxSize, $this->perPages)) {
            $maxSize = $this->perPage;
        }

        $this->perPage = $maxSize;
    }

    /**
     * 初始化工具按钮
     *
     */
    protected function setupTools()
    {
        if ($this->options['allowedRefresh']) {
            $label = trans('Refresh');

            Admin::script('$(document).on("pjax:complete",function(){ $(".refresh-grid").button("reset")});');

            $this->tools->prepend(
                "<button onclick='$(this).button(\"loading\");reload_grid();' class=\"refresh-grid btn btn-primary waves-effect\"><i class=\"zmdi zmdi-refresh-alt\"></i> $label</button>"
            );
        }

        if ($this->options['useTrash'] && $this->options['allowTrashEntry']) {
            $this->buildTrashEntry();
        }

        if ($this->options['useLayoutSwitcher']) {
            $this->tools->prepend(
                (new LayoutSwitcher($this))->render()
            );
        }

        if ($this->options['allowBatchDelete']) {
            $this->buildBatchDelete();
        }

        if (
            $this->options['allowedBatchRestore'] && $this->options['useTrash'] && $this->isTrash
        ) {
            $controller = __CONTROLLER__;
            $label = trans('Restore');

            $this->actions()->append("<a data-model='{$controller}' class='batch-restore' style='color:darkgreen'>{$label}</a>");
        }

        if (
            $this->options['allowedBatchDeletePermanently'] && $this->options['useTrash'] && $this->isTrash
        ) {
            $controller = __CONTROLLER__;
            $label = trans('Delete permanently');

            $this->actions()->append("<a data-model='{$controller}' class='batch-delete-permanently' style='color:#a00'>{$label}</a>");
        }

        if ($this->actions) {
            $this->tools->prepend($this->actions);
        }
    }

    protected function buildBatchDelete()
    {
        $controller = __CONTROLLER__;

        if ($this->options['useTrash']) {
            if (!$this->isTrash) {
                $label = trans('Move to trash');
                $action = 'batch-to-trash';
            } else {
                return;
            }
        } else {
            $action = 'batch-delete';
            $label = trans('Delete');
        }

        $this->actions()->append("<a data-model='{$controller}' class='$action'>{$label}</a>");
    }

    protected function buildTrashEntry()
    {
        $url = clone $this->url;
        $url->unsetQuery(static::$pjaxKey);

        $color = 'default';
        if ($this->isTrash) {
            $label = trans('List');
            $icon = 'fa fa-mail-reply';
            $url->unsetQuery(static::$trashKey);
        } else {
            $label = trans('Trash');
            $icon = 'fa fa-recycle';
            $url->query(static::$trashKey, 1);
        }

        $this->tools->prepend(
            "<div class='btn-group'><a class=\"btn btn-$color\" href=\"{$url->string()}\"><i class=\"$icon\"></i> {$label}</a></div>"
        );
    }

    /**
     * 设置或获取过滤器
     *
     * @return $this | Filter
     */
    public function filter(Filter $filter = null)
    {
        if ($filter) {
            $this->filter = $filter;
            $filter->setupConditions();
            $filter->grid($this);
            return $this;
        }

        return $this->filter;
    }

    /**
     *
     * @param Model $model
     * @return Model
     */
    public function model($model = null)
    {
        if (is_string($model)) {
            $this->model = model($model);
        } elseif ($model instanceof Model) {
            $this->model = $model;
        }

        if (! $this->model) {
            $this->model = model(Admin::model());
        }
        return $this->model;
    }

    /**
     * 构建where过滤条件数组
     *
     * @return array
     */
    protected function makeWhereContent()
    {
        if (! $this->filter) {
            return [];
        }

        // 构建where查询数组
        $where = [];
        foreach ($this->filter->conditions() as $condition) {
            if ($value = $condition->build()) {
                $where += $value;
            }
        }

        return $where;
    }

    /**
     * @return Tools
     */
    public function tools(\Closure $callback = null)
    {
        $callback && $callback($this->tools);

        return $this->tools;
    }

    /**
     * 构建排序数据
     *
     * @return string
     */
    protected function makeOrderContent()
    {
        if (! $sort = I('sort')) {
            $order = $this->options['defaultOrderByString'] ?: "`{$this->idName}` DESC";
        } else {
            $desc = I('desc');

            $sort = addslashes($sort);

            $order =  "`{$sort}` " . ($desc ? 'DESC' : 'ASC');
        }

        if ($this->orderByFilter) {
            return call_user_func($this->orderByFilter, $order, $sort);
        }
        return $order;
    }

    /**
     * 设置默认排序
     *
     * @param $order
     * @return $this
     */
    public function setDefaultOrderBy($order)
    {
        $this->options['defaultOrderByString'] = &$order;

        return $this;
    }

    /**
     * order by排序过滤器
     *
     * @param \Closure $filter
     * @return $this
     */
    public function orderByFilter(\Closure $filter)
    {
        $this->orderByFilter = $filter;

        return $this;
    }


    /**
     * 查询网格报表数据
     * model需要实现一个count方法和一个findList方法
     *
     * @return array
     */
    public function findList()
    {
        if ($this->rows) {
            return $this->rows;
        }

        $model = $this->model();

        $where = $this->makeWhereContent();

        $countMethod = 'count';
        $findMethod  = 'findList';
        if ($this->options['useTrash'] && $this->isTrash) {
            $countMethod = 'countTrash';
            $findMethod = 'findTrashList';
        }

        // 获取记录总条数
        $total = $model->$countMethod($where);

        $this->total($total);

        // 分页管理
        $pages = $this->paginator();

        if ($total && $this->options['usePagination']) {
            $this->pageString = $pages->make($total, $this->perPage);
        }

        // 生成分页字符串后获取当前分页（做过安全判断）
        $currentPage = $pages->current();

        $list = [];

        if ($total) {
            $list = $model->$findMethod(
                $where, $this->makeOrderContent(), ($currentPage - 1) * $this->perPage, $this->perPage
            );
        }

        return $this->rows = &$list;
    }

    /**
     * @param string $page
     * @return static|string
     */
    public function pageString($page = '')
    {
        if ($page) {
            $this->pageString = &$page;

            return $this;
        }
        return $this->pageString;
    }

    /**
     * @param null $total
     * @return $this|int
     */
    public function total($total = null)
    {
        if ($total !== null) {
            $this->total = &$total;

            return $this;
        }
        return $this->total;
    }

    /**
     * @return Table
     */
    public function table()
    {
        if (!$this->table) {
            $this->table = new Table();
            $this->table->grid($this);
        }

        return $this->table;
    }

    /**
     * @return Cards
     */
    public function card()
    {
        if (!$this->cards) {
            $this->cards = new Cards();
            $this->cards->grid($this);
        }

        return $this->cards;
    }

    /**
     * 是否启用分页
     *
     * @return bool
     */
    public function allowedPagination()
    {
        return $this->options['usePagination'];
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator($pages = null)
    {
        if ($pages) {
            $this->paginator = $pages;

            return $this;
        }

        if ($this->paginator) {
            return $this->paginator;
        }

        return $this->paginator = new Pages();
    }

    /**
     * Disable grid pagination.
     *
     * @return static
     */
    public function disablePagination()
    {
        $this->option('usePagination', false);

        return $this;
    }

    /**
     * @return static
     */
    public function disableEdit()
    {
        $this->options['allowEdit'] = false;
        return $this;
    }

    /**
     * @return static
     */
    public function disableCreate()
    {
        $this->options['allowCreate'] = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function allowBatchDelete()
    {
        $this->options['allowBatchDelete'] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableBatchDelete()
    {
        $this->options['allowBatchDelete'] = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableDelete()
    {
        $this->options['allowDelete'] = false;
        return $this;
    }


    /**
     * Disable row selector.
     *
     * @return $this
     */
    public function disableRowSelector()
    {
        return $this->option('useRowSelector', false);
    }

    /**
     * 禁止响应式输出table
     *
     * @return $this
     */
    public function disableResponsive()
    {
        $this->options['useRWD'] = false;
        return $this;
    }

    /**
     * 禁止使用pjax
     *
     * @return $this
     */
    public function disablePjax()
    {
        $this->options['pjax'] = false;

        $this->url->unsetQuery(static::$pjaxKey);

        return $this;
    }

    /**
     * @return bool
     */
    public function allowPjax()
    {
        return  $this->options['pjax'];
    }

    public function allowRestore()
    {
        $this->options['allowedRestore'] = true;
        return $this;
    }

    public function allowDeletePermanently()
    {
        $this->options['allowedDeletePermanently'] = true;
        return $this;
    }

    public function disableRestore()
    {
        $this->options['allowedRestore'] = false;
        return $this;
    }

    public function disableDeletePermanently()
    {
        $this->options['allowedDeletePermanently'] = false;
        return $this;
    }

    public function disableBatchDeletePermanently()
    {
        $this->options['allowedBatchDeletePermanently'] = false;
        return $this;
    }

    public function allowBatchDeletePermanently()
    {
        $this->options['allowedBatchDeletePermanently'] = true;
        return $this;
    }

    public function disableBatchRestore()
    {
        $this->options['allowedBatchRestore'] = false;
        return $this;
    }

    public function allowBatchRestore()
    {
        $this->options['allowedBatchRestore'] = true;
        return $this;
    }

    /**
     * Get or set option for grid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function option($key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key];
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     *
     * @return TrashButtons
     */
    public function trash(\Closure $closure = null)
    {
        if (!$this->trashButtons) {
            $this->trashButtons = new TrashButtons($this, $closure);
        }

        return $this->trashButtons;
    }

    /**
     * @return string
     */
    protected function renderTable()
    {
        $list = $this->findList();

        if (
            $this->isTrash && $list &&
            ($this->options['allowedRestore'] || $this->options['allowedDeletePermanently'])
        ) {
            $this->trash()->build();
        }

        if (
            !$this->isTrash && $list &&
            ($this->options['allowEdit'] || $this->options['allowDelete'] || $this->rowActions)
        ) {
            $this->buildRowActions();
        }

        $table = $this->table()->setRows($list)->render();

        $class = 'table-responsive';
        if (!$this->options['useRWD']) {
            $class = '';
        }

        return "<div class=\"table-rep-plugin\"><div class=\"$class\" data-pattern=\"priority-columns\">$table</div></div>";
    }

    /**
     * @return string
     */
    protected function renderCard()
    {
        $list = $this->findList();

        $card = $this->card();

        if ($list && ($this->options['allowEdit'] || $this->options['allowDelete'] || $this->rowActions)) {
            $card->setRowActions($this->rowActions());
        }

        return $card->setRows($list)->render();
    }

    /**
     * 判断是否是pjax请求
     *
     * @return bool
     */
    public static function isPjaxRequest()
    {
        return request()->isPjax();
    }

    /**
     * 获取行 actions对象
     *
     * @return RowActions
     */
    public function rowActions(\Closure $rendering = null)
    {
        return $this->rowActions ?: ($this->rowActions = new RowActions($this, $rendering));
    }

    /**
     * 创建行action按钮
     * 详情、删除
     */
    protected function buildRowActions()
    {
        $this->rowActions();
        $this->table()->append(function (Items $items, Td $td, Th $th, Tr $tr) {
            $th->value($this->rowActions->title());

            return $this->rowActions->setItems($items)->render();
        });
    }

    /**
     * 根据请求判断应该使用哪种布局
     *
     * @return void
     */
    public function setupLayoutForRequestParams()
    {
        if (I(static::$viewKey) == static::LAYOUT_CARD) {
            $this->layout = static::LAYOUT_CARD;
            $this->disableResponsive();
        } else {
            $this->layout = static::LAYOUT_TABLE;
        }
    }

    public function render()
    {
        if ($this->builded) {
            return '';
        }

        $this->builded = true;

        $isPjaxRequest = static::isPjaxRequest();

        if (! $isPjaxRequest) {
            $this->setupTools();
        }

        if ($this->layout == static::LAYOUT_CARD) {
            $this->url->query(static::$viewKey, static::LAYOUT_CARD);
            $content = $this->renderCard();
        } else {
            $this->url->query(static::$viewKey, static::LAYOUT_TABLE);
            $content = $this->renderTable();
        }

        if (!$this->options['allowDelete'] && !$this->options['useRowSelector']) {
            $this->disableGridScript();
        }

        $vars = [
            'content'     => &$content,
            'pageString'  => &$this->pageString,
            'pageOptions' => &$this->perPages,
            'perPage'     => &$this->perPage,
            'perPageKey'  => &$this->perPageKey,
            'url'         => $this->url,
            'filterId'    => '',
            'filter'      => '',
            'pjax'        => $this->options['pjax'],
            'useRWD'      => $this->options['useRWD']
        ];

        if ($isPjaxRequest) {
            return view('admin::grid-content', $vars)->render();
        }

        if ($this->options['useRWD']) {
            Admin::css('@lxh/plugins/RWD-Table-Patterns/dist/css/rwd-table.min');
            Admin::js('@lxh/plugins/RWD-Table-Patterns/dist/js/rwd-table.min');
        }

        if ($this->options['pjax']) {
            Admin::js('@lxh/js/jquery.pjax.min');
        }

        if ($this->options['indexScript']) {
            Admin::js($this->options['indexScript']);
        }

        if ($this->filter) {
            $vars['filterId'] = $this->filter->getContainerId();
            if ($this->filter->allowedInTable()) {
                $vars['filter'] = $this->filter->render();
            }
        }

        return $this->renderBox($vars);
    }

    /**
     * 渲染盒子
     *
     * @param array $vars
     * @return string
     */
    protected function renderBox(array &$vars)
    {
        $box = new Card();
        $box->setTools($this->tools)
            ->content(view($this->view, $vars)->render())
            ->style('inverse')
            ->btnToolbar();

        if ($btn = $this->buildCreateBtn()) {
            $box->rightTools()->append($btn);
        }

        if ($this->filter && $this->filter->allowedUseModal()) {
            $btn = new Button('<i class="fa fa-filter"></i> &nbsp;' . trans('Filter'));
            $btn->attribute('data-target', '#' . $this->filter->getContainerId())
                ->attribute('data-toggle', 'modal');

            $box->rightTools()->prepend($btn);
        }

        return $box->render();
    }

    protected function buildCreateBtn()
    {
        if (!$this->options['allowCreate']) {
            return;
        }

        $button = new Button(
            trans('Create ' . __CONTROLLER__), Admin::url()->action('create')
        );

        return $button
            ->attribute('data-action', 'create-row')
            ->name('create-' . __CONTROLLER__)
            ->icon('zmdi zmdi-playlist-plus')
            ->color('success')
            ->render();
    }

}
