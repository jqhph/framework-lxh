<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class RowSelector extends Widget
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Items
     */
    protected $items;

    /**
     * @var array
     */
    public static $scripts = [];

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return string
     */
    public function render()
    {
        return "<input name='tb-row[]' type='checkbox' value='{$this->items->get($this->grid->idName())}' {$this->formatAttributes()}/>";
    }

    /**
     * 设置行数据
     *
     * @param Items $items
     * @return $this
     */
    public function setItems(Items $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return string
     */
    public function renderHead()
    {
        return "<input type='checkbox' data-action='select-all' {$this->formatAttributes()}/>";
    }
}
