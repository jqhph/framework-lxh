<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Table\Table;
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

    public function usePagination($bool)
    {
        $this->options['usePagination'] = $bool;
        return $this;
    }

    public function allowEdit($bool)
    {
        $this->options['allowEdit'] = $bool;
        return $this;
    }
    public function allowCreate($bool)
    {
        $this->options['allowCreate'] = $bool;
        return $this;
    }
    public function allowDelete($bool)
    {
        $this->options['allowDelete'] = $bool;
        return $this;
    }
    public function useRWD($bool)
    {
        $this->options['useRWD'] = $bool;
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
            'createBtn' => $this->buildCreateBtn(),
        ], $this->options);

        return view($this->view, $vars)->render();
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
