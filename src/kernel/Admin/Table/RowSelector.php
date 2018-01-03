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
EOF;
/**
 * 行选择器点击功能js，以上为压缩版本，原版js如下：
 *
(function () {
var allInput = $('input[data-action="select-all"]')
// 选中所有行checkbox点击事件
allInput.click(function () {
    var _this = $(this), tb = _this.parent().parent().parent().parent(), inputs = tb.find('input[name="tb-row[]"]');
    if (_this.prop('checked')) {
        // 选中所有行，并把所有行的id存储到本按钮value中
        inputs.prop('checked', true);
        var ids = [], i, id;
        for (i in inputs) {
            if (typeof inputs[i] != 'object' || typeof inputs[i] == 'function' || typeof $(inputs[i]).val == 'undefined') continue;
            id = $(inputs[i]).val();
            if (!id || id == 'on') continue;
            ids.push(id);
            active($(inputs[i])) // 添加选中效果
        }
        set_all_input(ids.join(','));

    } else {
        inputs.prop('checked', false);
        _this.val('') // 清除值
        for (i in inputs) {
            if (typeof inputs[i] != 'object' || typeof inputs[i] == 'function' || typeof $(inputs[i]).val == 'undefined') continue;
            active($(inputs[i]), false) // 移除选中效果
        }
    }
});
function set_all_input(val) {
    allInput.val(val)
    $(document).trigger('grid.selected', val);
}
// 单行选中事件
$('input[name="tb-row[]"]').click(function () {
    var ids = allInput.val()
    ids = ids ? ids.split(',') : [];
    if ($(this).prop('checked')) {
        ids.push($(this).val());
        active($(this));
    } else {
        for(var i in ids) {
            if(ids[i] == $(this).val()) {
              ids.splice(i, 1);
              break;
            }
        }
        active($(this), false);
    }
    set_all_input(ids.join(','))
})
// 给当前行添加选中效果
function active(input, close) {
    if (input.data('action') == 'select-all') return;
    var tr = input.parent().parent();
    tr.removeClass('active');
    if (close !== false) tr.addClass('active');
}
})()
 */
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
