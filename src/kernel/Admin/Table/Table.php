<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Code;
use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Fields\Label;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Popover;
use Lxh\Admin\Fields\Switcher;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Fields\Checkbox;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\Support\Arr;

/**
 *
 * @method \Lxh\Admin\Table\Table link($field, $closure = null);
 * @method \Lxh\Admin\Table\Table button($field, $closure = null);
 * @method \Lxh\Admin\Table\Table label($field, $closure = null);
 * @method \Lxh\Admin\Table\Table tag($field, $closure = null);
 * @method \Lxh\Admin\Table\Table checkbox($field, $closure = null);
 * @method \Lxh\Admin\Table\Table code($field, $closure = null);
 * @method \Lxh\Admin\Table\Table image($field, $closure = null);
 * @method \Lxh\Admin\Table\Table expand($field, $closure = null);
 * @method \Lxh\Admin\Table\Table popover($field, $closure = null);
 * @method \Lxh\Admin\Table\Table editable($field, $closure = null);
 * @method \Lxh\Admin\Table\Table switch($field, $closure = null);
 * @method \Lxh\Admin\Table\Table checked($field);
 * @method \Lxh\Admin\Table\Table email($field);
 * @method \Lxh\Admin\Table\Table ip($field);
 */
class Table extends Widget
{
    /**
     * @var array
     */
    protected static $availableFields = [
        'link'     => Link::class,
        'button'   => Button::class,
        'label'    => Label::class,
        'tag'      => Tag::class,
        'checkbox' => Checkbox::class,
        'code'     => Code::class,
        'image'    => Image::class,
        'expand'   => Expand::class,
        'popover'  => Popover::class,
        'editable' => Editable::class,
        'switch'   => Switcher::class,
        'checked'  => 'checked',
        'email'    => 'email',
        'ip'       => 'ip',
    ];

    /**
     * @var Grid
     */
    protected $grid;

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
     * @var Grid\RowSelector
     */
    protected $rowSelector = null;

    /**
     * @var []
     */
    protected $nextRows = [];

    /**
     * @var int
     */
    protected $totalColumns = 0;

    /**
     * @var bool
     */
    protected $useQuickEdit = false;

    /**
     * 是否已设置默认排序
     *
     * @var bool
     */
    protected $settingsDefaultOrderBy = false;

    /**
     * Table constructor.
     *
     * @param array $headers
                [
                    '字段名' => [
                        'view' => '处理值显示方法参数',
                        'sortable' => '是否支持排序',
                        'desc' => '默认显示倒序，注意只有当sortable设置为true才有效，且不能多个字段同时设置为true',
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
        $this->attribute('id', 't'.Util::randomString(7));

        $this->class('table table-hover responsive');
    }

    /**
     * Register custom field.
     *
     * @param string $abstract
     * @param string $class
     *
     * @return void
     */
    public static function extend($abstract, $class)
    {
        static::$availableFields[$abstract] = $class;
    }

    /**
     * @param Grid|null $grid
     * @return $this|Grid
     */
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
    public function getKeyName()
    {
        return $this->grid->getKeyName();
    }

    /**
     * @param mixed $content
     * @param bool $active
     * @return $this
     */
    public function addExtraRow($content, $active = false)
    {
        $this->nextRows[] = ['content' => &$content, 'active' => $active];
        return $this;
    }

    /**
     * @return Grid\RowSelector
     */
    protected function selector()
    {
        if (! $this->rowSelector) {
            $this->rowSelector = new Grid\RowSelector($this->grid);
        }

        return $this->rowSelector;
    }

    /**
     * 设置字段
     *
     * @param string $field 字段名称
     * @return $this
     */
    public function column($field)
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

        if (strpos($view , '\\') !== false) {
            Admin::addAssetsFieldClass($view);
        }

        return $this;
    }

    /**
     * 当光标选中当前行时显示的内容
     *
     * @param $content
     * @return $this
     */
    public function hover($content)
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['expand'][] = &$content;
        return $this;
    }

    /**
     * 开启快速编辑模式
     *
     * @return $this
     */
    public function quickEdit()
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['quick-edit'][] = '<a class="hover quick-edit-btn">  &nbsp;<i class="fa fa-pencil"></i></a>';

        $this->useQuickEdit = true;

        return $this;
    }

    public function allowedQuickEdit()
    {
        return $this->useQuickEdit;
    }

    public function hoverAheadColumn($content)
    {
        foreach ($this->headers as $field => &$header) {
            if (!empty($header['hide'])) {
                continue;
            }

            $header['expand'][] = &$content;
            break;
        }

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
     * 设置默认排序为倒序
     *
     * @return $this
     */
    public function desc()
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['desc'] = 1;

        $this->setDefaultOrderBy(true);

        return $this;
    }

    /**
     * 设置默认排序为正序
     *
     * @return $this
     */
    public function asc()
    {
        if (! $this->field) {
            return $this;
        }
        $this->headers[$this->field]['asc'] = 1;

        $this->setDefaultOrderBy(false);

        return $this;
    }

    /**
     * 设置默认排序
     *
     * @param $desc
     */
    protected function setDefaultOrderBy($desc)
    {
        if ($this->settingsDefaultOrderBy) {
            throw new \RuntimeException('请勿同时设置多个默认排序字段');
        }
        $this->settingsDefaultOrderBy = true;

        $sortable = getvalue($this->headers[$this->field], 'sortable');

        $field = is_string($sortable) ? $sortable : $this->field;

        $this->grid->setDefaultOrderBy($field. ($desc ? ' DESC' : ' ASC'));
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

    /**
     *
     *
     * @return bool
     */
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

        foreach ($this->headers as $field => &$options) {
            $th .= $this->buildTh($field, $options)->render();
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

        if (getvalue($options, 'hide')) {
            $th->hide();
        }

        if ($sortFeild = getvalue($options, 'sortable')) {
            $th->sortable($sortFeild);
        }

        if (($desc = getvalue($options, 'desc')) !== null) {
            $th->desc();
        } elseif (($asc = getvalue($options, 'asc')) !== null) {
            $th->asc();
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
    protected function &buildRows()
    {
        $trString = '';
        foreach ($this->rows as $k => &$row) {
            if ($this->nextRows) {
                $trString .= $this->renderExtraRows();
            }

            $tr = $this->buildTr($k, $row);
            if ($this->handlers['tr']) {
                call_user_func($this->handlers['tr'], $tr);
            }

            $trString .= $tr->render();
        }
        if ($this->nextRows) {
            $trString .= $this->renderExtraRows();
        }
        return $trString;
    }

    protected function renderExtraRows()
    {
        $rows = '';
        foreach ($this->nextRows as &$row) {
            $class = '';
            if ($row['active']) {
                $class = 'class="active"';
            }

            $rows .= "<tr $class><td colspan='{$this->totalColumns()}' style='padding:0;border:0;'>{$row['content']}</td></tr>";
        }

        $this->nextRows = [];

        return $rows;
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
        return $this->trs[] = new Tr(
            $this,
            $k,
            $row,
            $this->columns
        );
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
            array_unshift($this->columns['front'], new Column(function (Items $items, Td $td, Th $th) {
                $selector = $this->selector();
                $selector->setItems($items);

                $th->value($selector->renderHead());

                $th->disableResponsive();
                return $selector->render();
            }));
        }

        $rows = $this->buildRows();
        $nodata = $rows ? '' : $this->noDataTip();

        return <<<EOF
<table {$this->formatAttributes()}><thead><tr>{$this->buildHeaders()}</tr></thead><tbody>{$rows}</tbody></table>$nodata       
EOF;
    }

    /**
     * @param $field
     * @return Table
     */
    public function text($field)
    {
        return $this->column($field);
    }

    /**
     * @param $field
     * @return $this
     */
    public function date($field)
    {
        return $this->column($field)->setFieldView('date');
    }

    /**
     * @param $field
     * @return $this
     */
    public function icon($field)
    {
        return $this->column($field)->setFieldView('icon');
    }


    /**
     * @param $field
     * @return $this
     */
    public function select($field)
    {
        return $this->column($field)->setFieldView('select');
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
        if (isset(static::$availableFields[$method])) {
            $field = getvalue($parameters, 0);
            if ($then = getvalue($parameters, 1)) {
                $this->headers[$field]['then'] = $then;
            }

            return $this->column($field)->setFieldView(static::$availableFields[$method]);
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
