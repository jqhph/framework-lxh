<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Admin;
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

    /**
     * @var \Lxh\Admin\Url
     */
    protected $url;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->url = Admin::url();
    }

    public function title()
    {
        return '';
    }

    public function row(array $row)
    {
        $this->row = &$row;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $allowEdit = $this->grid->allowEdit();
        $allowDelete = $this->grid->allowDelete();

        $id = get_value($this->row, $this->grid->idName());

        if ($allowEdit && $allowDelete) {
            return $this->renderEdit($id) . '&nbsp;&nbsp;&nbsp;&nbsp;' . $this->renderDelete($id);
        }

        if ($allowEdit) {
            return $this->renderEdit($id);
        }

        return $this->renderDelete($id);

    }

    protected function renderEdit($id)
    {
        $url = $this->url->detail($id);
        $label = trans('detail');

        $name = trim(str_replace('/', '-', $url), '-');

        $tabLabel = $this->normalizeTabLabel($id);

        return <<<EOF
<a onclick="open_tab('$name', '$url', '$tabLabel')">{$label}</a>
EOF;
    }

    protected function normalizeTabLabel($id)
    {
        $name = get_value($this->row, 'name') ?: $id;

        return $name . ' - ' . trans(Admin::model()) . trans('Edit');
    }

    protected function renderDelete($id)
    {
//        $url = Url::makeDetail($id, $module);
        $model = Admin::model();

        return <<<EOF
<a style="color:#ff5b5b" data-model="$model" data-action="delete-row" data-id="$id" href="javascript:"><i class="zmdi zmdi-close"></i></a>
EOF;

    }
}
