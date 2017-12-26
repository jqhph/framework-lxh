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

        if (!$name) return $tr;
        if (empty($this->row[$name]) || !is_array($this->row[$name])) return $tr;

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
        foreach ($headers as $k => &$options) {
            if (!isset($row[$k])) continue;

            $item = &$row[$k];

            if (!is_array($headers[$k])) {
                $td .= $this->buildTd($item, []);
                continue;
            }
            $view = get_value($headers[$k], 'view');
            if (! $view) {
                $td .= $this->buildTd($item, $headers[$k]);
                continue;
            }
            $td .= $this->buildTd(
                $this->renderFiledView($view, $k, $item, get_value($headers[$k], 'vars')),
                $headers[$k]
            );
        }
        return $td;
    }


    /**
     *
     * @return Field
     */
    protected function renderFiledView($view, $name, $value, $vars)
    {
        $method = 'build' . $view;
        if (method_exists($this, $method)) {
            return $this->$method($name, $value, $vars);
        }

        $view = str_replace('.', '\\', $view);

        $class  = "Lxh\\Admin\\Fields\\{$view}";

        return (new $class($name, $value, $vars))->render();
    }

    protected function buildTd($value, $options)
    {
        $priority = $this->table->getPriorityFromOptions($options);

        return "<td data-priority='$priority'>$value</td>";
    }
}
