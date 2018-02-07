<?php

namespace Lxh\Admin;

use Lxh\Admin\Cards\Cards;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Filter\AbstractFilter;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\RowActions;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Tools\Actions;
use Lxh\Admin\Tools\BatchDelete;
use Lxh\Admin\Tools\Tools;
use Lxh\Admin\Tools\TrTools;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Pages;
use Lxh\Contracts\Support\Renderable;
use Lxh\MVC\Model;

class Grid implements Renderable
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Cards
     */
    protected $cards;

    /**
     * Collection of all data rows.
     *
     * @var array
     */
    protected $rows;

    /**
     * All column names of the grid.
     *
     * @var array
     */
    public $columnNames = [];

    /**
     * Mark if the grid is builded.
     *
     * @var bool
     */
    protected $builded = false;

    /**
     * All variables in grid view.
     *
     * @var array
     */
    protected $variables = [];

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
    public $perPages = [10, 15, 20, 25, 30, 40, 50, 80, 100];

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
     * @var Tools
     */
    protected $tools;

    /**
     * @var \Lxh\Admin\Tools\Actions
     */
    protected $actions;

    /**
     * @var mixed
     */
    protected $paginator;

    /**
     * Options for grid.
     *
     * @var array
     */
    protected $options = [
        'usePagination' => true,
        'useFilter' => false,
        'useExporter' => true,
        'useActions' => true,
        'useRowSelector' => true,
        'allowEdit' => true,
        'allowDelete' => true,
        'allowCreate' => true,
        'allowBatchDelete' => false,
        'useRWD' => true,
        'indexScript' => '@lxh/js/public-index',
        'pjax' => true,
        'useLayoutSwitcher' => false,
    ];

    /**
     * @var string
     */
    protected $pageString = '';

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var string
     */
    protected $pjax = '_pjax';

    /**
     * current url
     *
     * @var \Lxh\Http\Url
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
     * Create a new grid instance.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array &$rows = [])
    {
        $this->rows = &$rows;
        $this->tools = new Tools();
        $this->url = new \Lxh\Http\Url();
        $this->idName = Admin::id();

        $this->setupPerPage();
        $this->url->query($this->pjax, static::getPjaxContainerId());

        $this->setupLayoutForRequestParams();
    }

    /**
     * 获取id键名
     *
     * @return string
     */
    public function idName()
    {
        return $this->idName;
    }

    /**
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'pjax-' . Admin::SPAID();
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
        if (! empty(I('view'))) {
            return $this;
        }
        $this->layout = 'card';
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
        if (! empty(I('view'))) {
            return $this;
        }
        $this->layout = 'table';
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
        if ($this->layout == 'card') {
            $this->card()->setRows($rows);
        } else {
            $this->table()->setRows($rows);
        }

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
        if ($this->layout == 'card') {
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
        if ($this->options['allowBatchDelete']) {
            $this->actions();
            $this->actions->append(new BatchDelete());
        }

        if ($this->actions) {
            $this->tools->prepend($this->actions);
        }
    }

    /**
     * 设置或获取过滤器
     *
     * @return $this | Filter
     */
    public function filter(Filter $filter = null)
    {
        if (! $this->filter) {
            $this->filter = $filter;
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
        if (! $sort = I('sort')) return "`{$this->idName}` DESC";

        $desc = I('desc');

        $sort = addslashes($sort);

        return "`{$sort}` " . ($desc ? 'DESC' : 'ASC');
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

        // 获取记录总条数
        $total = $model->count($where);

        $this->total($total);

        // 分页管理
        $pages = $this->paginator();

        if ($total && $this->allowedPagination()) {
            $this->pageString($pages->make($total, $this->perPage));
        }

        // 生成分页字符串后获取当前分页（做过安全判断）
        $currentPage = $pages->current();

        $list = [];

        if ($total) {
            $list = $model->findList(
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

        $this->url->unsetQuery($this->pjax);

        return $this;
    }

    /**
     * @return bool
     */
    public function allowPjax()
    {
        return  $this->options['pjax'];
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
     * @return string
     */
    protected function renderTable()
    {
        $list = $this->findList();

        if ($list && ($this->options['allowEdit'] || $this->options['allowDelete'] || $this->rowActions)) {
            $this->buildRowActions();
        }

        $table = $this->table()->setRows($list)->render();

        return "<div class=\"table-rep-plugin\"><div class=\"table-responsive\" data-pattern=\"priority-columns\">$table</div></div>";
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
        return isset($_GET['_pjax']);
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
        if (I('view') == 'card') {
            $this->useCard();
        } else {
            $this->useTable();
        }
    }

    /**
     * @return array|string
     */
    public function render()
    {
        if ($this->layout == 'card') {
            $content = $this->renderCard();
        } else {
            $content = $this->renderTable();
        }

        if (!$this->options['allowDelete'] && !$this->options['useRowSelector']) {
            $this->disableGridScript();
        }

        $vars = array_merge([
            'content' => &$content,
            'pageString'  => &$this->pageString,
            'pageOptions' => &$this->perPages,
            'perPage' => $this->perPage,
            'perPageKey' => $this->perPageKey,
            'url' => $this->url,
        ], $this->options);

        if (I($this->pjax)) {
            return view('admin::grid-content', $vars)->render();
        }

        $this->setupTools();

        $vars['filterId'] = '';
        if ($this->filter) {
            $vars['filterId'] = $this->filter->getContainerId();
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
        $box = new Box();
        $box->setTools($this->tools);

        $box->content(view($this->view, $vars)->render())->style('inverse')->btnToolbar();

        if ($btn = $this->buildCreateBtn()) {
            $box->rightTools()->append($btn);
        }

        if ($this->filter && $this->filter->allowedUseModal()) {
            $btn = new Button('<i class="fa fa-filter"></i> &nbsp;' . trans('Filter'));
            $btn->attribute('data-target', '#' . $this->filter->getContainerId());
            $btn->attribute('data-toggle', 'modal');

            $box->rightTools()->prepend($btn);
        }

        return $box->render();
    }

    protected function buildCreateBtn()
    {
        if (!$this->option('allowCreate')) {
            return;
        }

        $label = trans('Create ' . __CONTROLLER__);
        $button = new Button($label, Admin::url()->action('create'));

        $button->attribute('data-action', 'create-row');
        $button->name($this->getCreateBtnTabId());
        $button->icon('zmdi zmdi-playlist-plus');

        return $button->color('success')->render();
    }

    /**
     * @return string
     */
    protected function getCreateBtnTabId()
    {
        return  'create-' . __CONTROLLER__;
    }

}
