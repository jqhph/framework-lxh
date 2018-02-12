<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Th extends Widget
{
    /**
     * @var \Lxh\Admin\Table\Table
     */
    protected $table;

    /**
     * 字段名称
     *
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $value;

    /**
     * Is column sortable.
     *
     * @var bool
     */
    protected $sortable = false;

    /**
     * 默认是升序排序，所以点击时使用倒序
     *
     * @var int
     */
    protected $defaultDesc = 1;

    /**
     * 值为null表示使用默认排序
     *
     * @var array
     */
    protected $desc = null;

    public function __construct(Table $table = null, $name = null, array $attributes = [])
    {
        $this->table = $table;
        $this->field = $name;
        $this->attributes = &$attributes;

        $this->value($name);
        $this->show();
    }

    public function disableResponsive()
    {
        unset($this->attributes['data-priority']);

        return $this;
    }

    /**
     * @return $this
     */
    public function show()
    {
        $this->attributes['data-priority'] = 1;

        return $this;
    }

    /**
     * 默认隐藏列
     *
     * @return static
     */
    public function hide()
    {
        $this->attributes['data-priority'] = 0;

        return $this;
    }

    /**
     * @param string|callable $value
     * @return string|static
     */
    public function value($value = null)
    {
        if ($value === null) {
            return $this->value;
        }

        $this->value = $value instanceof \Closure ? $value($this) : trans($value, 'fields');

        return $this;
    }

    /**
     * 设置或获取字段名称
     *
     * @param string $field
     * @return string|static
     */
    public function field($field = null)
    {
        if ($field === null) {
            return $this->field;
        }
        $this->field = &$field;
        return $this;
    }

    /**
     * @param string $desc
     * @return $this
     */
    public function desc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Mark this column as sortable.
     *
     * @param string|null $field
     * @return $this
     */
    public function sortable($field = null)
    {
        $this->sortable = $field ?: true;

        return $this;
    }

    /**
     * Create the column sorter.
     *
     * @return string|void
     */
    public function sorter()
    {
        if (!$this->sortable) {
            return;
        }

        $icon = 'fa-arrows-v';

        if ($this->isSorted()) {
            $this->desc = I('desc', $this->desc);

            $icon = $this->desc ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc';
        }
        if ($this->desc !== null) {
            $icon = $this->desc ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc';
        }

        $url = url();

        $desc = $this->defaultDesc;// 默认升序排序
        if ($this->desc !== null) {
            $desc = !$this->desc;
        }

        $field = is_string($this->sortable) ? $this->sortable : $this->field;

        $url->query([
            'sort' => $field,
            'desc' => $desc
        ]);

        $url = $url->string();
        
        return "&nbsp;&nbsp;<a class=\"fa $icon\" href=\"$url\" style='color:#fe8f81'></a>";
    }

    /**
     * Determine if this column is currently sorted.
     *
     * @return bool
     */
    protected function isSorted()
    {
        $sort = I('sort');

        if ($sort) {
            $this->desc = null;
        }

        return $sort == $this->field;
    }

    public function render()
    {
        if (! $this->value) {
            $this->disableResponsive();
        }

        return "<th {$this->formatAttributes()}>{$this->value}{$this->sorter()}</th>";
    }

}
