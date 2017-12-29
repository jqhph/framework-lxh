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

    protected $toTimestamp = false;

    protected function buildCondition($field)
    {
        $start = I($field . '-start');
        $end = I($field . '-end');

        if ($start === '' && $end === '') {
            return null;
        }

        if ($start && $end) {
            if ($this->toTimestamp) {
                $start = strtotime($start);
                $end = strtotime($end);
            }
            return ['between', [$start, $end]];
        }

        if ($start) {
            if ($this->toTimestamp) {
                $start = strtotime($start);
            }
            return ['>', $start];
        }

        if ($this->toTimestamp) {
            $end = strtotime($end);
        }

        return ['<', $end];
    }

    /**
     * 是否把值转化为时间戳
     *
     * @return static
     */
    public function toTimestamp()
    {
        $this->toTimestamp = true;

        return $this;
    }
}
