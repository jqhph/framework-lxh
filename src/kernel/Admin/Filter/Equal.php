<?php

namespace Lxh\Admin\Filter;

class Equal extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@equal';

    protected function condition($field)
    {
        $value = I($field);

        return $value === '' ? null : $value;
    }

}
