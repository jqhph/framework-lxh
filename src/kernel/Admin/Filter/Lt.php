<?php

namespace Lxh\Admin\Filter;

class Lt extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@lt';

    protected function buildCondition($field, $input)
    {
        return ['<', $input];
    }
}
