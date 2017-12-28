<?php

namespace Lxh\Admin\Filter;

class Ilike extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@ilike';

    protected function condition($field)
    {
        $value = I($field);

        return $value === '' ? null : ['ilike', "%$value%"];
    }
}
