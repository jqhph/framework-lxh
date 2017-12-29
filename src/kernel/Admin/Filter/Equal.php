<?php

namespace Lxh\Admin\Filter;

class Equal extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@eq';

    protected function buildCondition($field)
    {
        $value = I($field);

        if ($value === '' || $value === null) {
            return null;
        }

        if (is_array($value)) {
            $count = count($value);

            if ($count < 1) return null;

            return count($value) > 1 ? ['IN', &$value] : $value[0];
        }

        return $value;
    }

}
