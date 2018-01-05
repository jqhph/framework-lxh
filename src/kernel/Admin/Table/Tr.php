<?php

namespace Lxh\Admin\Table;

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
     * @var array
     */
    protected $row;

    /**
     * 额外的字段
     *
     * @var array
     */
    protected $columns = [];

    /**
     * 字段自定义配置键值
     *
     * @var string
     */
    protected $fieldOptionsKey = 'options';

    public function __construct(Table $table, $offset, &$row, array $columns = [])
    {
        $this->table = $table;
        $this->offset = $offset;
        $this->row = &$row;
        $this->columns = &$columns;
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
     * @return string
     */
    public function render()
    {
        $columns = $this->buildColumns($this->row);

        $tr = "<tr {$this->formatAttributes()}>{$columns}</tr>";

        $name = $this->table->treeName();

        if (!$name || empty($this->row[$name]) || !is_array($this->row[$name])) {
            return $tr;
        }

        $tr .= $this->buildTree($name, $this->row[$name])->render();

        return $tr;
    }

    /**
     * @return array
     */
    public function row()
    {
        return $this->row;
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
        return new Tree($this, $name, $this->offset + 1, $rows);
    }

    /**
     * @param array $row
     * @return string
     */
    protected function buildColumns(array &$row)
    {
        $tdString = '';

        $this->prependColumns($tdString, $row);

        $headers = $this->table->headers();
        $counter = 1;
        foreach ($headers as $field => &$options) {
            if (!isset($row[$field])) {
                continue;
            }
            if (isset($this->columns['mid'][$counter])) {
                while ($column = get_value($this->columns['mid'], $counter)) {
                    $tdString .= $this->columns['mid'][$counter]->tr($this)->row($row)->render();
                    $counter++;
                }
            }

            $this->renderColumns($tdString, $row, $field, $options);

            $counter ++;
        }

        foreach ($this->columns['mid'] as $k => $column) {
            if ($k > $counter) {
                $tdString .= $column->tr($this)->row($row)->render();
            }
        }

        $this->appendColumns($tdString, $row);

        return $tdString;
    }

    /**
     * @param $tdString
     * @param $row
     * @param $field
     * @param $options
     */
    protected function renderColumns(&$tdString, &$row, &$field, &$options)
    {
        $td = $this->buildTd($field, $row[$field]);

        if ($handler = $this->table->handler('field', $field)) {
            // 自定义处理器
            if (!is_string($handler) && is_callable($handler)) {
                $this->setupTdWithOptions($td, $options);
                $td->value(
                    $handler($row[$field], $td, $this)
                );
                $tdString .= $td->render();
            } else {
                $tdString .=  $this->setupTdWithOptions($td, $options)->value($handler)->render();
            }
            return;
        }

        if (! $options || ! is_array($options)) {
            $tdString .= $td->render();
            return;
        }

        $view = get_value($options, 'view');
        if (! $view) {
            $tdString .= $this->setupTdWithOptions($td, $options)->render();
            return;
        }
        $tdString .= $this->renderFiledView($view, $field, $row[$field], $td, $options);
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
    protected function prependColumns(&$tdString, &$row)
    {
        foreach ($this->columns['front'] as $column) {
            $tdString .= $column->tr($this)->row($row)->render();
        }
    }

    /**
     * 新建额外的列
     *
     * @return void
     */
    protected function appendColumns(&$tdString, &$row)
    {
        foreach ($this->columns['last'] as $column) {
            $tdString .= $column->tr($this)->row($row)->render();
        }
    }


    /**
     *
     * @return string
     */
    protected function renderFiledView($view, $field, $value, Td $td, $vars)
    {
        $method = 'build' . $view;
        if (method_exists($this, $method)) {
            return $td->value($this->$method($field, $value, get_value($vars, $this->fieldOptionsKey)))->render();
        }

        $view = str_replace('.', '\\', $view);

        if (strpos($view, '\\') !== false) {
            $class = $view;
        } else {
            $class  = "Lxh\\Admin\\Fields\\{$view}";
        }

        return $td->value((new $class($field, $value, get_value($vars, $this->fieldOptionsKey)))->render())->render();
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
