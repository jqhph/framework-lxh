<?php

namespace Lxh\Admin\Form\Field;

class Decimal extends Text
{

    public function render()
    {
        $this->prepend('<i class="fa fa-terminal"></i>');

        return parent::render();
    }
}
