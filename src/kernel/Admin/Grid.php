<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Filter\AbstractFilter;
use Lxh\Admin\Table\Actions;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Contracts\Support\Renderable;
use Lxh\Admin\Kernel\Url;
use Lxh\MVC\Model;

class Grid implements Renderable
{
    /**
     * @var string
     */
    protected $module = __CONTROLLER__;

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
        'allowBatchDelete' => true,
        'useRWD'           => true,
        'usePublicJs'      => true,
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
     * Create a new grid instance.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array $headers = [], array &$rows = [])
    {
        $this->table = new Table($headers, $rows);
        $this->rows = &$rows;

        $this->table->grid($this);

        $this->setupPerPage();
    }

    /**
     * 模块名称
     *
     * @return string
     */
    public function module()
    {
        return $this->module;
    }

    /**
     * 设置或获取id键名
     *
     * @param string $name
     * @return static|string
     */
    public function idName($name = null)
    {
        if ($name === null) {
            return $this->idName;
        }
        $this->idName = $name;

        return $this;
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
     * 设置自定义处理字段渲染方法
     *
     * @param string $field
     * @param string|callable $content
     * @return static
     */
    public function field($field, $content)
    {
        $this->table->value($field, $content);

        return $this;
    }

    /**
     * 增加额外的列
     *
     * @param string|callable $title 标题或回调函数
     * @param string|callable $content 内容或回调函数
     * @return static
     */
    public function column($title, $content = null)
    {
        $this->table->column($title, $content);

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

    protected function setupPerPage()
    {
        $maxSize = I($this->perPageKey);

        if (!in_array($maxSize, $this->perPages)) {
            $maxSize = $this->perPage;
        }

        $this->perPage = $maxSize;
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
        if ($model) {
            $this->model = $model;
        }
        if (! $this->model) {
            $this->model = create_model(__CONTROLLER__);
        }
        return $this->model;
    }

    protected function makeWhereContent()
    {
        if (! $this->filter) {
            return [];
        }

        // 格式化查询数组
        foreach ($this->filter->conditions() as $condition) {
            $condition->build();
        }

        return AbstractFilter::getConditionsValue();
    }

    protected function makeOrderContent()
    {
        if (! $sort = I('sort')) return 'id DESC';

        $desc = I('desc');

        $sort = addslashes($sort);

        return "`{$sort}`" . ($desc ? 'DESC' : 'ASC');
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
        $order = $this->makeOrderContent();

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
            $list = $model->findList($where, $order, ($currentPage - 1) * $this->perPage, $this->perPage);
        }

        return $this->rows = &$list;
    }

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
     * @return static
     */
    public function disableUseRWD()
    {
        $this->options['useRWD'] = false;
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

    protected function getTableString()
    {
        $list = $this->findList();
        

        if ($this->options['allowEdit'] || $this->options['allowDelete']) {
            $this->buildActions();
        }

        return $this->table->setRows($list)->render();
    }

    protected function buildActions()
    {
        $action = new Actions($this);

        $this->table->column($action->title(), function (array $row, Column $column) use ($action) {
            $action->row($row);

            return $action->render();
        });
    }

    public function render()
    {
        $table = $this->getTableString();

        $vars = array_merge([
            'table' => &$table,
            'page'  => &$this->pageString,
            'pages' => &$this->perPages,
            'url'   => $this->formatUrl(),
            'perPage' => $this->perPage,
            'perPageKey' => $this->perPageKey
        ], $this->options);

        $box = new Box();

        $box->content(view($this->view, $vars)->render())->style('inverse')->btnToolbar();

        if ($btn = $this->buildCreateBtn()) {
            $box->tool($btn);
        }

        return $box->render();
    }

    protected function formatUrl()
    {
        $url = '';
        if ($this->pageString && $this->total > 0 && $this->usePagination()) {
            $url = url()->unsetQuery($this->perPageKey)->string();
        }

        return $url;
    }

    protected function buildCreateBtn()
    {
        if (!$this->option('allowCreate')) {
            return;
        }

        $label = trans('Create ' . $this->module);
        return (new Button($label, Url::makeAction('create', $this->module), [
            'color' => 'success',
            'id' => $this->getCreateBtnTabId(),
        ]))->render();
    }

    protected function getCreateBtnTabId()
    {
        return  'create-' . $this->module;
    }
}
