<?php

namespace Lxh\Admin\Filter;

use Lxh\Admin\Admin;
use Lxh\Support\Arr;

class Between extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@between';

    protected function condition($field)
    {
        $start = I($field . '-start');
        $end = I($field . '-end');

        if ($start === '' && $end === '') {
            return null;
        }

        if ($start && $end) {
            return ['between', [$start, $end]];
        }

        if ($start) return ['>', $start];

        return ['<', $end];
    }
}
