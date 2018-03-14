<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Grid;

class TrashButtons
{
    protected $grid;

    protected $rendering;

    protected $allowRestore;

    // Delete permanently
    protected $allowDeletePermanently;

    protected $defaultCans = [
        'restore' => false,
        'deletePermanently' => false
    ];

    public function __construct(Grid $grid, \Closure $rendering = null)
    {
        $this->grid = $grid;
        $this->rendering = $rendering;

        $this->allowRestore = $this->defaultCans['restore']
            = $this->grid->option('allowedRestore');

        $this->allowDeletePermanently = $this->defaultCans['deletePermanently']
            = $this->grid->option('allowedDeletePermanently');
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

    public function build()
    {
        $this->grid->table()->hoverAheadColumn(function (Items $items) {
            if ($r = $this->rendering) {
                $r($this, $items);
            }

            $id = $items->get($this->grid->idName());

            $model = __CONTROLLER__;

            if ($this->allowRestore && $this->allowDeletePermanently) {
                $restore = trans('Restore');
                $delete = trans('Delete permanently');

                $content = "<a data-id='$id' data-action='restore' data-model='$model' style='color:green;'>$restore</a>
| <a data-id='$id' data-action='delete-permanently' data-model='$model' style='color:#a00'>$delete</a>";

            } elseif ($this->allowRestore) {
                $restore = trans('Restore');

                $content = "<a data-id='$id' data-action='restore' data-model='$model' style='color:darkgreen;'>$restore</a>";

            } elseif ($this->allowDeletePermanently) {
                $delete = trans('Delete permanently');

                $content = "<a data-id='$id' data-action='delete-permanently' data-model='$model' style='color:#a00'>$delete</a>";

            }

            // 重置
            $this->allowRestore           = $this->defaultCans['restore'];
            $this->allowDeletePermanently = $this->defaultCans['deletePermanently'];

            return $content;
        });


    }
}
