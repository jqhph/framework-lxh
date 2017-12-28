<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Str;

class MultipleSelect extends Field\MultipleSelect
{
    use Condition;

    protected $view = 'admin::filter.multiple-select';

    protected $width = ['field' => 3];


    protected function variables()
    {
        $name = $this->name();
        if ($value = I($name)) {
            $this->value = $value;
        }
        $this->value = (array) $this->value;

        return array_merge(parent::variables(), [
            'filterInput' => $this->getInputHandler()->render()
        ]);
    }
}
