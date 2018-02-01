<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Code;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Fields\Helper;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Fields\Label;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Profile;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Fields\Checkbox;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Support\Arr;

/**
 *
 * @method Table link($field, $closure = null);
 * @method Table button($field, $closure = null);
 * @method Table label($field, $closure = null);
 * @method Table tag($field, $closure = null);
 * @method Table checkbox($field, $closure = null);
 * @method Table code($field, $closure = null);
 * @method Table image($field, $closure = null);
 * @method Table expand($field, $closure = null);
 * @method Table helper($field, $closure = null);
 * @method Table checked($field);
 * @method Table email($field);
 */
class Table extends Widget
{
    /**
     * @var array
     */
    protected static $fields = [
        'link' => Link::class,
        'button' => Button::class,
        'label' => Label::class,
        'tag' => Tag::class,
        'checkbox' => Checkbox::class,
        'code' => Code::class,
        'image' => Image::class,
        'expand' => Expand::class,
        'helper' => Helper::class,
        'checked' => 'checked',
        'email' => 'email',
    ];


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
    protected $columns = [
        'front' => [],
        'last' => [],
        'mid' => [],
    ];

    /**
     * @var array
     */
    protected $handlers = [
        'field' => [],
        'th' => [],
        'tr' => null,
    ];

    /**
     * 当前正在设置的字段名称
     *
     * @var string
     */
    protected $field;

    /**
     * @var RowSelector
     */
    protected $rowSelector = null;

    /**
     * @var string
     */
    protected $nextRow = '';

    /**
     * @var int
     */
    protected $totalColumns = 0;

    /**
     * Table constructor.
     *
     * @param array $headers
                [
                    '字段名' => [
                        'view' => '处理值显示方法参数',
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

        $this->class('table table-hover responsive');
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
     * @return array
     */
    public function columns()
    {
        return $this->columns;
    }

    /**
     * 获取id键名
     *
     * @return string
     */
    public function idName()
    {
        return $this->grid->idName();
    }

    /**
     * @param $content
     * @return $this
     */
    public function addExtraRow($content)
    {
        $this->nextRow = &$content;
        return $this;
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
     * 设置字段
     *
     * @param string $field 字段名称
     * @return $this
     */
    public function field($field)
    {
        if (! isset($this->headers[$field])) {
            $this->headers[$field] = [];
        }
        $this->field = &$field;
        return $this;
    }

    /**
     * 设置字段渲染view
     *
     * @param string $view
     * @return $this
     */
    protected function setFieldView($view)
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['view'] = &$view;

        return $this;
    }

    /**
     * 自定义字段渲染处理方法
     *
     * @param $content
     * @return $this
     */
    public function display($content)
    {
        if (! $this->field) {
            return $this;
        }
        return $this->setHandler('field', $this->field, $content);
    }

    /**
     * 设置字段为可排序
     *
     * @param string $field
     * @return $this
     */
    public function sortable($field = null)
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['sortable'] = $field ?: 1;
        return $this;
    }

    /**
     * 设置隐藏字段
     * 当Table设置为允许响应式时有效
     *
     * @return $this
     */
    public function hide()
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['hide'] = 1;
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
     * 追加列到指定位置
     *
     * @param int $position 位置，从1开始
     * @param string|callable $title 标题或回调函数
     * @param string|callable $content 内容或回调函数
     * @return Column
     */
    public function column($position, $title, $content = null)
    {
        if ($position < 1) {
            throw new InvalidArgumentException('位置参数错误，请传入大于0的整数');
        }
        if (isset($this->columns['mid'][$position])) {
            throw new InvalidArgumentException('该位置己存在其他列');
        }

        return $this->columns['mid'][intval($position)] = new Column($title, $content);
    }

    /**
     * 追加列到最前面
     *
     * @param string|callable $title 标题或回调函数
     * @param string|callable $content 内容或回调函数
     * @return Column
     */
    public function prepend($title, $content = null)
    {
        return $this->columns['front'][] = new Column($title, $content);
    }

    /**
     * 追加列到最后面
     *
     * @param string|callable $title 标题或回调函数
     * @param string|callable $content 内容或回调函数
     * @return Column
     */
    public function append($title, $content = null)
    {
        return $this->columns['last'][] = new Column($title, $content);
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
                $new[$v] = [];
            } else {
                $new[$k] = (array)$v;
            }
        }
        $this->headers = &$new;
    }

    /**
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * @param mixed $content
     * @return static
     */
    public function th($content)
    {
        if (! $this->field) {
            return $this;
        }

        return $this->setHandler('th', $this->field, $content);
    }

    /**
     * 自定义行处理器
     *
     * @param callable $callback
     * @return static
     */
    public function tr(callable $callback)
    {
        $this->handlers['tr'] = $callback;
        return $this;
    }

    /**
     * @param $name
     * @param $key
     * @param $handler
     * @return $this
     */
    protected function setHandler($name, $key, &$handler)
    {
        $this->handlers[$name][$key] = &$handler;

        return $this;
    }

    /**
     * @param $name
     * @param $k
     * @return mixed
     */
    public function handler($name, $k)
    {
        if (! isset($this->handlers[$name])) return null;

        return isset($this->handlers[$name][$k]) ? $this->handlers[$name][$k] : null;
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

        // 额外追加的列
        foreach ($this->columns['front'] as &$column) {
            $th .= $column->title();
        }

        $counter = 1;
        foreach ($this->headers as $field => &$options) {
            if (isset($this->columns['mid'][$counter])) {
                while ($column = get_value($this->columns['mid'], $counter)) {
                    $th .= $this->columns['mid'][$counter]->title();
                    $counter++;
                }
            }

            $th .= $this->buildTh($field, $options)->render();

            $counter++;
        }

        // 额外追加的列
        foreach ($this->columns['mid'] as $k => $column) {
            if ($k > $counter) {
                $th .= $column->title();
            }
        }

        // 额外追加的列
        foreach ($this->columns['last'] as &$column) {
            $th .= $column->title();
        }
        return $th;
    }

    /**
     *
     * @param string $field 字段名称
     * @param array $field 配置参数
     * @return Th
     */
    protected function buildTh($field, &$options = [])
    {
        $th = $this->ths[$field] = new Th($this, $field);

        if (get_isset($options, 'hide')) {
            $th->hide();
        }

        if ($field = get_isset($options, 'sortable')) {
            $th->sortable($field);
        }

        if (($desc = get_isset($options, 'desc')) !== null) {
            $th->desc($desc);
        }

        if ($handler = $this->handler('th', $field)) {
            // 自定义处理器
            if (!is_string($handler) && is_callable($handler)) {
                call_user_func($handler, $th);
            } else {
                $th->value($handler);
            }
        }

        return $th;
    }

    /**
     * @return string
     */
    protected function buildRows()
    {
        $trString = '';
        foreach ($this->rows as $k => &$row) {
            if ($this->nextRow) {
                $trString .= "<tr><td colspan='{$this->totalColumns()}' style='padding:0;border:0;'>{$this->nextRow}</td></tr>";
                $this->nextRow = '';
            }

            $tr = $this->buildTr($k, $row);
            if ($this->handlers['tr']) {
                call_user_func($this->handlers['tr'], $tr, $row);
            }

            $trString .= $tr->render();
        }
        return $trString;
    }

    /**
     *
     * @return int
     */
    public function totalColumns()
    {
        if ($this->totalColumns)
            return $this->totalColumns;

        return $this->totalColumns =
              count($this->headers)
            + count($this->columns['mid'])
            + count($this->columns['front'])
            + count($this->columns['last']);
    }

    /**
     * @param $k
     * @param $row
     * @return \Lxh\Admin\Table\Tr
     */
    protected function buildTr($k, &$row)
    {
        return $this->trs[] = new Tr($this, $k, $row, $this->columns, $this->handlers);
    }

    /**
     * Render the table.
     *
     * @return string
     */
    public function render()
    {
        if ($this->allowRowSelector()) {
            // 添加行选择器到列最前面
            array_unshift($this->columns['front'], new Column(function (array $row, Td $td, Th $th) {
                $selector = $this->selector();
                $selector->row($row);

                $th->value($selector->renderHead());

                $th->disableResponsive();
                return $selector->render();
            }));
        }

        // 指定位置的额外添加列，需要按键值大小升序排序
        if ($this->columns['mid']) {
            ksort($this->columns['mid'], 1);
        }

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

    /**
     * @param $field
     * @return Table
     */
    public function text($field)
    {
        return $this->field($field);
    }

    /**
     * @param $field
     * @return $this
     */
    public function date($field)
    {
        return $this->field($field)->setFieldView('date');
    }

    /**
     * @param $field
     * @return $this
     */
    public function icon($field)
    {
        return $this->field($field)->setFieldView('icon');
    }


    /**
     * @param $field
     * @return $this
     */
    public function select($field)
    {
        return $this->field($field)->setFieldView('select');
    }

    /**
     * @return string
     */
    protected function noDataTip()
    {
        $tip = trans('No Data.');
        return <<<EOF
<div style="margin:15px 0 0 25px;"><span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{$tip}</span></div>
EOF;

    }

    public function __call($method, $parameters)
    {
        if (isset(static::$fields[$method])) {
            $field = get_value($parameters, 0);
            if ($then = get_value($parameters, 1)) {
                $this->headers[$field]['then'] = $then;
            }

            return $this->field($field)->setFieldView(static::$fields[$method]);
        }

        $p = count($parameters) > 0 ? $parameters[0] : true;
        if ($method == 'class') {
            if (isset($this->attributes[$method])) {
                $this->attributes[$method] = "{$this->attributes[$method]} $p";
            } else {
                $this->attributes[$method] = &$p;
            }
            return $this;
        }
    }

}
