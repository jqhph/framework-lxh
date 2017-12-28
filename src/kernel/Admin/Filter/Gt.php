<?php

namespace Lxh\Admin\Filter;

class Gt extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@gt';

    protected function condition($field)
    {
        $value = I($field);

        return $value === '' ? null : ['>', $value];
    }
}
