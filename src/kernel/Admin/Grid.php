<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Filter\AbstractFilter;
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
     * Collection of all grid columns.
     *
     * @var array
     */
    protected $columns;

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

    protected $pageString = '';

    protected $total = 0;

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

        $this->setupPerPage();
    }

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
        if (! $sort = I('sort')) return '';

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
     * @return $this
     */
    public function disablePagination()
    {
        $this->option('usePagination', false);

        return $this;
    }

    public function allowEdit()
    {
        return $this->options['allowEdit'];
    }
    public function disableEdit()
    {
        $this->options['allowEdit'] = false;
        return $this;
    }

    public function allowCreate()
    {
        return $this->options['allowCreate'];
    }

    public function disableCreate()
    {
        $this->options['allowCreate'] = false;
        return $this;
    }

    public function allowDelete()
    {
        return $this->options['allowDelete'];
    }

    public function disableDelete()
    {
        $this->options['allowDelete'] = false;
        return $this;
    }


    /**
     * Disable row selector.
     *
     * @return Grid|mixed
     */
    public function disableRowSelector()
    {

        return $this->option('useRowSelector', false);
    }

    public function useRWD()
    {
        return $this->options['useRWD'];
    }

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

    public function render()
    {
        $list = $this->findList();

        if (count($list) < $this->perPage) {
            $this->perPages = '';
        }

        $vars = array_merge([
            'table' => $this->table->setRows($list)->render(),
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
