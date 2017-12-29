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

    protected $table;

    protected $level = 1;

    protected $row;

    public function __construct(Table $table, $level, &$row, $attributes = [])
    {
        $this->table = $table;
        $this->level = $level;
        $this->row = &$row;

        parent::__construct((array) $attributes);
    }

    public function render()
    {
        $tr = '<tr>' . $this->buildColumns($this->row) . '</tr>';

        $name = $this->table->treeName();

        if (!$name || empty($this->row[$name]) || !is_array($this->row[$name])) {
            return $tr;
        }

        $tr .= $this->buildTree($name, $this->row[$name])->render();

        return $tr;
    }

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
        return new Tree($this, $name, $this->level + 1, $rows);
    }

    protected function buildColumns(array &$row)
    {
        $td = '';
        $headers = $this->table->headers();
        foreach ($headers as $field => &$options) {
            if (!isset($row[$field])) {
                continue;
            }

            $item = &$row[$field];

            if (is_callable($options)) {
                // 自定义处理
                $td .= $this->buildTd(call_user_func($options, $field, $item));
                continue;
            }

            if (isset($options['handler']) && is_callable($options['handler'])) {
                // 自定义处理
                $td .= $this->buildTd(call_user_func($options['handler'], $field, $item));
                continue;
            }

            if (!is_array($options)) {
                $td .= $this->buildTd($item);
                continue;
            }

            $view = get_value($options, 'view');
            if (! $view) {
                $td .= $this->buildTd($item, $options);
                continue;
            }
            $td .= $this->buildTd(
                $this->renderFiledView($view, $field, $item, get_value($options, 'options')),
                $options
            );
        }
        return $td;
    }


    /**
     *
     * @return Field
     */
    protected function renderFiledView($view, $field, $value, $vars)
    {
        $method = 'build' . $view;
        if (method_exists($this, $method)) {
            return $this->$method($field, $value, $vars);
        }

        $view = str_replace('.', '\\', $view);

        $class  = "Lxh\\Admin\\Fields\\{$view}";

        return (new $class($field, $value, $vars))->render();
    }

    protected function buildTd($value, &$options = [])
    {
        $priority = $this->table->getPriorityFromOptions($options);

        return "<td data-priority='$priority'>$value</td>";
    }
}
