<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Contracts\Support\Renderable;
use Lxh\Admin\Kernel\Url;

class Grid implements Renderable
{
    /**
     * @var string
     */
    protected $module = __CONTROLLER__;

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
     * @var \Lxh\Admin\Grid\Filter
     */
    protected $filter;

    /**
     * Resource path of the grid.
     *
     * @var
     */
    protected $resourcePath;

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
    protected $view = 'component.grid';

    /**
     * Per-page options.
     *
     * @var array
     */
    public $perPages = [10, 20, 30, 50];

    /**
     * Default items count per-page.
     *
     * @var int
     */
    public $perPage = 20;


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
     * Create a new grid instance.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array $headers, array $rows)
    {
        $this->table = new Table($headers, $rows);
        $this->rows = &$rows;
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
        return new Tools\Paginator($this);
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
        $vars = array_merge([
            'table' => $this->table->render(),
//            'createBtn' => $this->buildCreateBtn(),
        ], $this->options);

        $box = new Box();

        $box->content(view($this->view, $vars)->render())->style('inverse');

        if ($btn = $this->buildCreateBtn()) {
            $box->tool($btn);
        }

        return $box->render();
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
