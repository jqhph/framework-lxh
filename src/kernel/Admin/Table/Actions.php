<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Fields\Field;
use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Actions
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * 当前行数据
     *
     * @var array
     */
    protected $row = [];

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function title()
    {
        return '';
    }

    public function row(array $row)
    {
        $this->row = &$row;
    }

    /**
     * @return string
     */
    public function render()
    {
        $allowEdit = $this->grid->allowEdit();
        $allowDelete = $this->grid->allowDelete();

        $id = get_value($this->row, 'id');
        $module = $this->grid->module();

        if ($allowEdit && $allowDelete) {
            return $this->renderEdit($id, $module) . '&nbsp;&nbsp;&nbsp;&nbsp;' . $this->renderDelete($id, $module);
        }

        if ($allowEdit) {
            return $this->renderEdit($id, $module);
        }

        return $this->renderDelete($id, $module);

    }

    protected function renderEdit($id, $module)
    {
        $url = Url::makeDetail($id, $module);
        $label = trans('detail');

        $name = trim(str_replace('/', '-', $url), '-');

        $tabLabel = $this->normalizeTabLabel($id, $module);

        return <<<EOF
<a onclick="open_tab('$name', '$url', '$tabLabel')">{$label}</a>
EOF;
    }

    protected function normalizeTabLabel($id, $module)
    {
        $name = get_value($this->row, 'name') ?: $id;

        return $name . ' - ' . trans($module) . trans('Edit');
    }

    protected function renderDelete($id, $module)
    {
//        $url = Url::makeDetail($id, $module);

        return <<<EOF
<a style="color:#ff5b5b" data-model="$module" data-action="delete-row" data-id="$id" href="javascript:"><i class="zmdi zmdi-close"></i></a>
EOF;

    }
}
