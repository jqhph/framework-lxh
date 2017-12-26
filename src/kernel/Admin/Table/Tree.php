<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Tree 
{
    protected $tr;

    /**
     * 缩进字段
     *
     * @var string
     */
    protected $indentField = 'name';

    protected $spacing;

    /**
     * 存储层级树数据字段名
     *
     * @var string
     */
    protected $name;

    /**
     * 层级
     *
     * @var int
     */
    protected $level = 1;
    
    protected $rows = [];
    
    public function __construct(Tr $tr, $name, $level, array &$rows)
    {
        $this->tr = $tr;
        $this->name = $name;
        $this->level = $level;
        $this->rows = &$rows;

        $this->spacing = $this->level * 3;
    }

    /**
     * 缩进处理
     *
     * @param mixed $value
     * @paran bool $end
     * @return string
     */
    protected function formatIndent($value, $end = false)
    {
        $indent = str_repeat('&nbsp;', $this->spacing);

        if ($end) {
            return "{$indent}└─ {$value}";
        }
        return "{$indent}├─ {$value}";
    }


    protected function buildTr($k, &$row)
    {
        return new Tr($this->tr->table(), $k, $row);
    }

    public function render()
    {
        $tr = '';
        $endPos = count($this->rows) - 1;
        foreach ($this->rows as $k => &$row) {
            if (isset($row[$this->indentField])) {
                $row[$this->indentField] = $this->formatIndent($row[$this->indentField], $endPos == $k);
            }
            $tr .= $this->buildTr($k + $this->level + 1, $row)->render();
        }
        return $tr;
    }
}
