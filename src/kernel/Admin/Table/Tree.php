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
    protected $tier = 1;

    /**
     * @var array
     */
    protected $rows = [];
    
    public function __construct(Tr $tr, $name, $tier, array &$rows)
    {
        $this->tr = $tr;
        $this->name = $name;
        $this->tier = $tier;
        $this->rows = &$rows;

        $this->spacing = $this->tier * 5;
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


    /**
     * @param $k
     * @param $row
     * @return Tr
     */
    protected function buildTr($k, &$row)
    {
        $tb = $this->tr->table();
        return (new Tr($tb, $k, $row, $tb->columns()))->setTier($this->tier + 1);
    }

    /**
     * @return string
     */
    public function render()
    {
        $tr = '';
        $endPos = count($this->rows) - 1;
        foreach ($this->rows as $k => &$row) {
            if (isset($row[$this->indentField])) {
                $row[$this->indentField] = $this->formatIndent($row[$this->indentField], $endPos == $k);
            }
            $tr .= $this->buildTr($k + $this->tier + 1, $row)->render();
        }
        return $tr;
    }
}
