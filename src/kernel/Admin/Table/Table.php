<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Fields\Field;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Table extends Widget
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $view = 'admin::table';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $style = [];

    /**
     * @var array
     */
    protected $ths = [];

    /**
     * @var array
     */
    protected $trs = [];

    /**
     * @var int
     */
    protected $defaultPriority = 1;

    /**
     * @var string
     */
    protected $treeName = '';

    /**
     * 额外增加的列
     *
     * @var array
     */
    protected $columns = [];

    /**
     * 字段渲染处理器
     *
     * @var array
     */
    protected $handlers = [];

    protected $rowSelector = null;

    /**
     * Table constructor.
     *
     * @param array $headers
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
     * @param array $rows
     * @param array $style
     */
    public function __construct(&$headers = [], &$rows = [], $style = [])
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->setStyle($style);
    }

    public function grid(Grid $grid = null)
    {
        if ($grid !== null) {
            $this->grid = $grid;
            return $this;
        }
        return $this->grid;
    }

    /**
     * 获取id键名
     *
     * @return static|string
     */
    public function idName()
    {
        return $this->grid->idName();
    }

    /**
     * @return RowSelector
     */
    protected function selector()
    {
        if (! $this->rowSelector) {
            $this->rowSelector = new RowSelector($this);
        }

        return $this->rowSelector;
    }

    /**
     * 设置自定义处理字段渲染方法
     *
     * @param string $field
     * @param string|callable $content
     * @return static
     */
    public function value($field, $content)
    {
        $this->handlers[$field] = $content;

        return $this;
    }


    /**
     * 使用层级树结构
     *
     * @param string $name 字段名
     * @return static
     */
    public function useTree($name)
    {
        $this->treeName = $name;

        return $this;
    }

    /**
     * 树状字段键名
     *
     * @return string
     */
    public function treeName()
    {
        return $this->treeName;
    }

    /**
     * 增加额外的列
     *
     * @param string|callable $title 标题或回调函数
     * @param string|callable $content 内容或回调函数
     * @return Column
     */
    public function column($title, $content = null)
    {
        $column = new Column($title, $content);

        return $this->columns[] = $column;
    }

    public function allowRowSelector()
    {
        return $this->grid->option('useRowSelector');
    }

    /**
     * Set table headers.
     *
     * @param array $headers header
     *
     * @return $this
     */
    public function setHeaders($headers = [])
    {
        $this->headers = &$headers;

        if ($this->headers) {
            $this->normalizeHeaders();
        }

        return $this;
    }

    /**
     * 格式化数组
     *
     * @return array
     */
    protected function normalizeHeaders()
    {
        $new = [];
        foreach ($this->headers as $k => &$v) {
            if (is_int($k) && is_string($v)) {
                if (! $v) continue;
                $new[$v] = '';
            } else {
                $new[$k] = $v;
            }
        }
        $this->headers = &$new;
    }

    public function headers()
    {
        return $this->headers;
    }

    /**
     * Set table rows.
     *
     * @param array $rows
     *
     * @return $this
     */
    public function setRows(&$rows = [])
    {
        if (Arr::isAssoc($rows)) {
            foreach ($rows as $key => &$item) {
                $this->rows[] = [$key, $item];
            }

            return $this;
        }

        $this->rows = &$rows;

        return $this;
    }

    /**
     * Set table style.
     *
     * @param array $style
     *
     * @return $this
     */
    public function setStyle($style = [])
    {
        $this->style = $style;

        return $this;
    }

    protected function buildHeaders()
    {
        $th = '';

        if ($this->allowRowSelector()) {
            // 构建行选择器
            $th .= $this->buildTh($this->selector()->renderHead());
        }

        foreach ($this->headers as $k => &$options) {
            $th .= $this->buildTh($k, $options)->render();
        }

        foreach ($this->columns as $column) {
            $th .= $column->title();
        }
        return $th;
    }

    /**
     *
     * @param string $name 字段名称
     * @param array $field 配置参数
     * @return Th
     */
    protected function buildTh($name, &$options = [])
    {
        $vars = (array)get_value($options, 'th');

        $vars['data-priority'] = $this->getPriorityFromOptions($options);

        $th = $this->ths[$name] = new Th($this, $name, $vars);

        if (get_isset($options, 'sortable')) {
            $th->sortable();
        }
    
        if (($desc = get_isset($options, 'desc')) !== null) {
            $th->desc($desc);
        }

        return $th;
    }

    public function getPriorityFromOptions($options)
    {
        return get_value($options, 'show', $this->defaultPriority);
    }

    protected function buildRows()
    {
        $tr = '';
        foreach ($this->rows as $k => &$row) {
            $tr .= $this->buildTr($k, $row)->render();
        }
        return $tr;
    }


    protected function buildTr($k, &$row)
    {
        $columns = [
            'before' => [],
            'after' => $this->columns,
        ];

        if ($this->allowRowSelector()) {
            $columns['before'][] = $this->selector();
        }

        return $this->trs[] = new Tr($this, $k, $row, $columns, $this->handlers);
    }


    /**
     * Render the table.
     *
     * @return string
     */
    public function render()
    {
        $rows = $this->buildRows();
        $nodata = $rows ? '' : $this->noDataTip();

        $vars = [
            'attributes' => $this->formatAttributes(),
            'headers' => $this->buildHeaders(),
            'rows' => &$rows,
            'nodata' => &$nodata
        ];
        return view($this->view, $vars)->render();
    }

    protected function noDataTip()
    {
        $tip = trans('No Data.');
        return <<<EOF
            <tr><td></td><td data-priority="1"><span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{$tip}</span></td></tr>
EOF;

    }
}
