<?php

namespace Lxh\Admin\Filter;

class Like extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@like';

    protected function condition($field)
    {
        $value = I($field);

        return $value === '' ? null : ['like', "%$value%"];
    }
}
