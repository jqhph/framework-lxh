<?php

namespace Lxh\Admin;

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
use Lxh\Admin\Widgets\Box;
use Lxh\Contracts\Support\Renderable;
use Lxh\Admin\Kernel\Url;
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
     * Options for grid.
     *
     * @var array
     */
    protected $options = [
        'usePagination'    => true,
        'useFilter'        => false,
        'useExporter'      => true,
        'useActions'       => true,
        'useRowSelector'   => true,
        'allowEdit'        => true,
        'allowDelete'      => true,
        'allowCreate'      => true,
        'allowBatchDelete' => false,
        'useRWD'           => true,
        'indexScript'      => 'view/public-index',
        'pjax'             => true,
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
     * @var string
     */
    protected $pjaxContainer = '#pjax-container';

    /**
     * current url
     *
     * @var \Lxh\Http\Url
     */
    protected $url;

    /**
     * Create a new grid instance.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array $headers = [], array &$rows = [])
    {
        $this->table = new Table($headers);
        $this->rows = &$rows;
        $this->table->grid($this);
        $this->tools = new Tools();
        $this->url = request()->url();
        $this->idName = Admin::id();

        $this->setupPerPage();
        $this->url->query($this->pjax, $this->pjaxContainer);
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
     * @return \Lxh\Admin\Tools\Actions
     */
    public function actions()
    {
        if (! $this->actions) {
            $this->actions = new Actions();
        }

        return $this->actions;
    }

    /**
     * 设置表格行数据
     *
     * @param array $rows
     * @return static
     */
    public function rows(array &$rows)
    {
        $this->table()->setRows($rows);
        $this->rows = &$rows;

        return $this;
    }

    /**
     *
     * @param array $headers 支持参数说明
    [
        '字段名' => [
            'view' => '处理值显示方法参数',
            'th' => '头部属性配置',
            'options' => ['view的配置参数'],
            'sortable' => '是否支持排序',
            'desc' => '默认显示倒序，注意只有当sortable设置为true才有效，且不能多个字段同时设置为true',
            'show' => '默认是显示，传0或false则隐藏字段，注意当使用RWD-table插件时此值才有效'
        ],
    ]
    */
    public function headers(array $headers)
    {
        $this->table->setHeaders($headers);
        return $this;
    }

    /**
     * 初始化每页显示行数
     */
    protected function setupPerPage()
    {
        $maxSize = I($this->perPageKey);

        if (!in_array($maxSize, $this->perPages)) {
            $maxSize = $this->perPage;
        }

        $this->perPage = $maxSize;
    }

    protected function setupTools()
    {
        if ($this->options['allowBatchDelete']) {
            $this->tools->append(new BatchDelete());
        }

        if ($this->actions) {
            $this->tools->prepend($this->actions);
        }
        
    }

    /**
     * 设置或获取过滤器
     *
     * @return static | Filter
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
            $this->model = create_model($model);
        } elseif ($model instanceof Model) {
            $this->model = $model;
        }

        if (! $this->model) {
            $this->model = create_model(Admin::model());
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
                $where = array_merge($where, $value);
            }
        }

        return $where;
    }

    /**
     * @return Tools
     */
    public function tool()
    {
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

        if ($total && $this->usePagination()) {
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
        return $this->table;
    }

    /**
     * 是否启用分页
     *
     * @return bool
     */
    public function usePagination()
    {
        return $this->options['usePagination'];
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        return pages();
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

    public function allowEdit()
    {
        return $this->options['allowEdit'] === true;
    }

    public function allowDelete()
    {
        return $this->options['allowDelete'] === true;
    }

    /**
     * @return static
     */
    public function disableDelete()
    {
        $this->options['allowDelete'] = false;
        return $this;
    }


    /**
     * Disable row selector.
     *
     * @return static
     */
    public function disableRowSelector()
    {
        return $this->option('useRowSelector', false);
    }

    /**
     * 禁止响应式输出table
     *
     * @return static
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

    protected function getTableString()
    {
        $list = $this->findList();

        if ($list && $this->options['allowEdit'] || $this->options['allowDelete']) {
            $this->buildRowActions();
        }

        return $this->table->setRows($list)->render();
    }

    /**
     * 创建行action按钮
     * 详情、删除
     */
    protected function buildRowActions()
    {
        $action = null;

        $this->table->append(function (array $row, Td $td, Th $th, Tr $tr) use($action) {
            if (! $action) $action = new RowActions($this);

            $th->value($action->title());

            return $action->row($row)->render();
        });
    }

    public function render()
    {
        $table = $this->getTableString();

        if (!$this->allowDelete() && !$this->options['useRowSelector']) {
            $this->disableGridScript();
        }

        $vars = array_merge([
            'table' => &$table,
            'page'  => &$this->pageString,
            'pages' => &$this->perPages,
            'perPage' => $this->perPage,
            'perPageKey' => $this->perPageKey
        ], $this->options);

        if (I($this->pjax)) {
            return view('admin::grid-content', $vars)->render();
        }

        $this->setupTools();

        return $this->renderBox($vars);
    }

    protected function renderBox(array &$vars)
    {
        $box = new Box();
        $box->setTools($this->tools);

        $box->content(view($this->view, $vars)->render())->style('inverse')->btnToolbar();

        if ($btn = $this->buildCreateBtn()) {
            $box->rightTools()->append($btn);
        }

        return $box->render();
    }

    protected function buildCreateBtn()
    {
        if (!$this->option('allowCreate')) {
            return;
        }

        $model = Admin::model();

        $label = trans('Create ' . $model);
        $button = new Button($label, Admin::url()->action('create'));

        $button->attribute('data-action', 'create-row');
        $button->name($this->getCreateBtnTabId());

        return $button->color('success')->render();
    }

    protected function getCreateBtnTabId()
    {
        return  'create-' . Admin::model();
    }
}
