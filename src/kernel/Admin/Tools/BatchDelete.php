<?php

namespace Lxh\Admin\Tools;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Button;
use Lxh\Contracts\Support\Renderable;

class BatchDelete implements Renderable
{
    protected $id = 'batch-delete';

    public function label()
    {
        return trans('Batch Remove');
    }

    public function render()
    {
        $btn = new Button($this->label());

        $btn->attribute('data-model', Admin::model());
        $btn->attribute('id', $this->id);

        return $btn->color('danger')->icon('fa fa-trash')->render();

    }
}
