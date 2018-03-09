<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Grid;
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

    protected $allowRestore;

    // Delete permanently
    protected $allowDeletePermanently;

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

        if ($this->grid->isTrash()) {
            $auth = auth();

            $this->allowRestore = $auth->can(__CONTROLLER__.'.restore');
            $this->allowDeletePermanently = $auth->can(__CONTROLLER__.'.delete-permanently');
        }
    }

    /**
     * @return \Lxh\Admin\Data\Items
     */
    public function items()
    {
        return $this->items;
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
    public function allowRestore()
    {
        $this->allowRestore = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableRestore()
    {
        $this->allowRestore = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function allowDeletePermanently()
    {
        $this->allowDeletePermanently = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableDeletePermanently()
    {
        $this->allowDeletePermanently = false;
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
     * @return mixed|null
     */
    public function getId()
    {
        return $this->items->get(
            $this->grid->idName()
        );
    }

    protected function buildTrash($id)
    {
        if ($this->allowRestore && $this->allowDeletePermanently) {
            $restore = trans('Restore');
            $delete = trans('Delete permanently');

            $model = __CONTROLLER__;

            $this->prepend(
                "<a data-action='restore' data-model='$model'>$restore</a> | <a data-action='delete-permanently' data-model='$model' style='color:#a00'>$delete</a>"
            );
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        // 重置所有工具
        $this->tools = [];

        $id = $this->getId();

        if ($rendering = $this->rendering) {
            $rendering($this, $this->items);
        }

        if ($this->grid->isTrash()) {
            $this->buildTrash($id);

        } else {
            if ($this->allowEdit && $this->allowDelete) {
                $this->prepend(
                    $this->renderEdit($id)
                    . '&nbsp;&nbsp;&nbsp;' . $this->renderDelete($id)
                );
            } elseif ($this->allowEdit) {
                $this->prepend($this->renderEdit($id));
            } elseif ($this->allowDelete) {
                $this->prepend($this->renderDelete($id));
            }

            // 重置状态
            $this->allowEdit = $this->grid->option('allowEdit');
            $this->allowDelete = $this->grid->option('allowDelete');

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

        $name = trim(str_replace('/', '-', $url), '-');

        return "<a onclick=\"open_tab('$name', '$url', '{$this->normalizeTabLabel($id)}')\"><i class=\"fa fa-edit\" style=\"color:#333\"></i></a>";
    }

    /**
     * @param $id
     * @return string
     */
    protected function normalizeTabLabel($id)
    {
        $name = $this->items->get('name') ?: $this->items->get('title', $id);

        return trim(str_replace('&nbsp;', '', $name))
        . ' - ' . trans(__CONTROLLER__) . trans('Edit');
    }

    /**
     * @param $id
     * @return string
     */
    protected function renderDelete($id)
    {
        $model = __CONTROLLER__;

        return <<<EOF
<a style="font-size:15px" data-model="$model" data-action="delete-row" data-id="$id" href="javascript:"><i class="red zmdi zmdi-close-circle-o"></i></a>
EOF;

    }
}
