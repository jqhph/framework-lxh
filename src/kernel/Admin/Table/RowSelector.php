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

        Admin::script($this->setupScript());
    }

    protected function setupScript()
    {

        return <<<EOF
        
(function () {
var allInput = $('input[data-action="select-all"]')
allInput.click(function () {
    var _this = $(this), tb = _this.parent().parent().parent().parent(), inputs = tb.find('input[name="tb-row[]"]');
    if (_this.prop('checked')) {
        inputs.prop('checked', true);
        var ids = [], i, id;
        for (i in inputs) {
            if (typeof inputs[i] != 'object' || typeof inputs[i] == 'function' || typeof $(inputs[i]).val == 'undefined') continue;
            id = $(inputs[i]).val();
            if (!id || id == 'on') continue;
            ids.push(id);
        }
        _this.val(ids.join(','))
    } else {
        inputs.prop('checked', false);
        _this.val('')
    }
});
$('input[name="tb-row[]"]').click(function () {
    var ids = allInput.val()
    ids = ids ? ids.split(',') : [];
    if ($(this).prop('checked')) {
        ids.push($(this).val())
        ids = array_unique(ids)
    } else {
        for(var i in ids) {
            if(ids[i] == $(this).val()) {
              ids.splice(i, 1);
              break;
            }
        }
    }
    allInput.val(ids.join(','))
})
})()
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

        return "<input type='checkbox' data-action='select-all' {$attr} /><input type='hidden' id='select-all' value=''>";
    }
}
