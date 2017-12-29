<?php

namespace Lxh\Admin\Filter;

class Gt extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@gt';

    protected function buildCondition($field)
    {
        $value = I($field);

        return ($value === '' || $value === null) ? null : ['>', $value];
    }
}
