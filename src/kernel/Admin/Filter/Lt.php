<?php

namespace Lxh\Admin\Filter;

class Lt extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@lt';

    protected function condition($field)
    {
        $value = I($field);

        return $value === '' ? null : ['<', $value];
    }
}
