<?php

namespace Lxh\Admin\Form\Field;

class Number extends Text
{
    public function render()
    {
        $this->number();

        return parent::render();
    }
}
