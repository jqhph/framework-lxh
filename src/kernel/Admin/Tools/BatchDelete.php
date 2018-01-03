<?php

namespace Lxh\Admin\Tools;

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

        return $btn->attribute('id', $this->id)->color('danger')->icon('fa fa-trash')->render();

    }
}
