<?php

namespace Lxh\Admin\Filter;

class Equal extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@eq';

    protected function buildCondition($field, $input)
    {
        if (is_array($input)) {
            $count = count($input);

            if ($count < 1) return null;

            return count($input) > 1 ? ['IN', &$input] : $input[0];
        }

        return $input;
    }

}
