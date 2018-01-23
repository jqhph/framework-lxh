<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Tools\Tools;
use Lxh\Admin\Tools\TrTools;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class RowActions extends TrTools
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var \Lxh\Admin\Url
     */
    protected $url;

    /**
     * @var bool
     */
    protected $allowEdit;

    /**
     * @var bool
     */
    protected $allowDelete;

    public function __construct(Grid $grid, \Closure $rendering = null)
    {
        $this->grid = $grid;
        $this->allowEdit = $this->grid->option('allowEdit');
        $this->allowDelete = $this->grid->option('allowDelete');
        $this->url = Admin::url();
        $this->rendering = $rendering;
    }

    public function title()
    {
        return '';
    }

    /**
     * @return $this
     */
    public function allowEdit()
    {
        $this->allowEdit = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function allowDelete()
    {
        $this->allowDelete = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableDelete()
    {
        $this->allowDelete = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableEdit()
    {
        $this->allowEdit = false;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->tools = [];

        $id = $this->row($this->grid->idName());

        if ($this->allowEdit && $this->allowDelete) {
            $this->prepend($this->renderEdit($id) . '&nbsp;&nbsp;&nbsp;' . $this->renderDelete($id));
        } elseif ($this->allowEdit) {
            $this->prepend($this->renderEdit($id));
        } elseif ($this->allowDelete)
            $this->prepend($this->renderDelete($id));

        if ($rendering = $this->rendering) {
            $rendering($this, $this->tr);
        }

        $end = '&nbsp;&nbsp;&nbsp;';
        $tools = '';
        foreach ($this->tools as &$tool) {
            if ($tool instanceof Renderable) {
                $tools .= $tool->render() . $end;
            } elseif ($tool instanceof \Closure) {
                $tools = $tool($this) . $end;
            } else {
                $tools .= $tool . $end;
            }
        }

        return rtrim($tools, $end);
    }

    protected function renderEdit($id)
    {
        $url = $this->url->detail($id);
        $label = '<i class="fa fa-edit" style="color:#188ae2"></i>';//trans('detail');

        $name = trim(str_replace('/', '-', $url), '-');

        $tabLabel = $this->normalizeTabLabel($id);

        return <<<EOF
<a onclick="open_tab('$name', '$url', '$tabLabel')">{$label}</a>
EOF;
    }

    protected function normalizeTabLabel($id)
    {
        $name = $this->row('name') ?: $id;

        return trim(str_replace('&nbsp;', '', $name)) . ' - ' . trans(__CONTROLLER__) . trans('Edit');
    }

    protected function renderDelete($id)
    {
//        $url = Url::makeDetail($id, $module);
        $model = __CONTROLLER__;

        return <<<EOF
<a style="color:#ff5b5b" data-model="$model" data-action="delete-row" data-id="$id" href="javascript:"><i class="zmdi zmdi-close"></i></a>
EOF;

    }
}
