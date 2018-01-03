<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Admin;
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
     * @var Table
     */
    protected $table;

    protected $row = [];

    public static $scripts = [];

    public function __construct(Table $table)
    {
        $this->table = $table;

//        Admin::script($this->setupScript());
    }

    protected function setupScript()
    {
        return <<<EOF
EOF;
    }

    public function render()
    {
        $id = get_value($this->row, $this->table->idName());

        $attr = $this->formatAttributes();

        return "<input name='tb-row[]' type='checkbox' value='$id' {$attr} />";
    }

    /**
     * 设置行数据
     *
     * @param array $row
     * @return static
     */
    public function row(array &$row)
    {
        $this->row = $row;

        return $this;
    }

    public function renderHead()
    {
        $attr = $this->formatAttributes();

//        return <<<EOF
//<div style="padding:0;margin:0 0 0 20px;" class="checkbox checkbox-custom"><input type="checkbox"><label style="padding:0;min-height:15px"></label></div>
//EOF;
        return "<input type='checkbox' data-action='select-all' {$attr} /><input type='hidden' id='select-all' value=''>";
    }
}
