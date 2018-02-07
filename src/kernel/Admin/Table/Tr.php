<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Fields\Traits\Builder;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Admin\Table\Tree;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Tr extends Widget
{
    use Builder;

    /**
     * @var \Lxh\Admin\Table\Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $tds = [];

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * 行数据
     *
     * @var Items
     */
    protected $items;

    /**
     * 额外的字段
     *
     * @var array
     */
    protected $columns = [];

    /**
     * 层级
     *
     * @var int
     */
    protected $tier = 1;

    public function __construct(Table $table, $offset, &$row, array $columns = [])
    {
        $this->table = $table;
        $this->offset = $offset;
        $this->items = new Items($row, $offset);
        $this->columns = &$columns;
    }

    public function setTier($tier)
    {
        $this->tier = $tier;
        return $this;
    }

    /**
     * 获取行号
     *
     * @return int
     */
    public function line()
    {
        return $this->offset + 1;
    }

    /**
     *
     * @param $content
     * @return $this
     */
    public function next($content)
    {
        $this->table->addExtraRow($content);
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $columns = $this->buildColumns();

        $tr = "<tr {$this->formatAttributes()}>{$columns}</tr>";

        $name = $this->table->treeName();

        if (!$name || empty($this->row[$name]) || !is_array($this->row[$name])) {
            return $tr;
        }

        $tr .= $this->buildTree($name, $this->row[$name])->render();

        return $tr;
    }

    /**
     * 获取当前行的某一列数据
     *
     * @return mixed
     */
    public function item($key)
    {
        return $this->items->get($key);
    }

    /**
     * @return \Lxh\Admin\Table\Table
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * 树状结构
     *
     * @param string $name
     * @param array $rows
     * @return Tree
     */
    protected function buildTree($name, &$rows)
    {
        return new Tree($this, $name, $this->tier, $rows);
    }

    /**
     * @param array $row
     * @return string
     */
    protected function buildColumns()
    {
        $tdString = '';

        $this->prependColumns($tdString);

        $headers = $this->table->headers();
        $counter = 1;
        foreach ($headers as $field => &$options) {
            if (isset($this->columns['mid'][$counter])) {
                while ($column = get_value($this->columns['mid'], $counter)) {
                    $tdString .= $this->columns['mid'][$counter]->tr($this)->render();
                    $counter++;
                }
            }

            $this->renderColumns($tdString, $field, $options);

            $counter ++;
        }

        foreach ($this->columns['mid'] as $k => $column) {
            if ($k > $counter) {
                $tdString .= $column->tr($this)->render();
            }
        }

        $this->appendColumns($tdString);

        return $tdString;
    }

    /**
     * @param $tdString
     * @param $field
     * @param $options
     */
    protected function renderColumns(&$tdString, &$field, &$options)
    {
        $value = $this->items->get($field);

        $td = $this->buildTd($field, $value);

        if ($handler = $this->table->handler('field', $field)) {
            // 自定义处理器
            if (!is_string($handler) && is_callable($handler)) {
                $this->setupTdWithOptions($td, $options);
                $td->value(
                    $handler($value, $td, $this)
                );
                $tdString .= $td->render();
            } else {
                $tdString .=  $this->setupTdWithOptions($td, $options)->value($handler)->render();
            }
            return;
        }

        // 没有定义视图，字段原样显示
        if (! $options || ! is_array($options)) {
            $tdString .= $td->render();
            return;
        }

        $view = get_value($options, 'view');
        if (! $view) {
            $tdString .= $this->setupTdWithOptions($td, $options)->render();
            return;
        }

        // 定义了视图
        $tdString .= $this->renderFiledView(
            $view,
            $field,
            $value,
            $td,
            get_value($options, 'then')
        );
    }

    /**
     * 设置td配置
     *
     * @param Td $td
     * @param $options
     * @return Td
     */
    protected function setupTdWithOptions(Td $td, &$options)
    {
        return $td;
    }

    /**
     * @param $tdString
     * @param $row
     */
    protected function prependColumns(&$tdString)
    {
        foreach ($this->columns['front'] as $column) {
            $tdString .= $column->tr($this)->render();
        }
    }

    /**
     * @return Items
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * 新建额外的列
     *
     * @return void
     */
    protected function appendColumns(&$tdString)
    {
        foreach ($this->columns['last'] as $column) {
            $tdString .= $column->tr($this)->render();
        }
    }


    /**
     * @param string|object $view
     * @param string $field
     * @param mixed $value
     * @param Td $td
     * @param \Closure $then
     * @return mixed|string
     */
    protected function renderFiledView($view, $field, $value, Td $td, \Closure $then = null)
    {
        $method = 'build' . $view;
        if (method_exists($this, $method)) {
            return $td->value($this->$method($field, $value))->render();
        }

        $view = str_replace('.', '\\', $view);

        if (strpos($view, '\\') !== false) {
            $class = $view;
        } else {
            $class  = "Lxh\\Admin\\Fields\\{$view}";
        }

        $view = new $class($field, $value);

        $view->setTable($this->table)
            ->setContainerId(
                $this->table->getAttribute('id')
            )
            ->setItems($this->items);

        if ($then) $then($view, $this);

        return $td->value($view->render())->render();
    }

    /**
     * @param $field
     * @param $value
     * @param array $options
     * @return Td
     */
    protected function buildTd($field, $value)
    {
        return $this->tds[$field] = new Td($value);
    }
}
