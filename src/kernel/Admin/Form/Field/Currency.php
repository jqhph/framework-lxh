<?php

namespace Lxh\Admin\Form\Field;

class Currency extends Text
{
    protected $symbol = '$';

    public function symbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function prepare($value)
    {
        return (float) $value;
    }

    public function render()
    {

        $this->prepend($this->symbol);

        return parent::render();
    }
}
