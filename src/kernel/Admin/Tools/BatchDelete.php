<?php

namespace Lxh\Admin\Tools;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Code;
use Lxh\Contracts\Support\Renderable;

class BatchDelete implements Renderable
{
    protected $id = 'batch-delete';

    public function label()
    {
        return trans('Delete');
    }

    public function render()
    {
        $controller = __CONTROLLER__;

        return "<a data-model='{$controller}' class='{$this->id}'>{$this->label()}</a>";
    }
}
