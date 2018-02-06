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

    protected function buildCondition($field, $input)
    {
        $start = I($field . '-start');
        $end = I($field . '-end');

        $validateStart = ($start !== null && $start !== '');
        $validateEnd = ($end !== null && $end !== '');

        if (!$validateStart && !$validateEnd) {
            return null;
        }

        if ($validateStart && $validateEnd) {
            if ($this->toTimestamp) {
                $start = strtotime($start);
                $end = strtotime($end);
            }
            return ['between', [$start, $end]];
        }

        if ($validateStart) {
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
     * 设置用户输入的值有效
     *
     * @param $field
     * @return bool
     */
    protected function inputInvalid($field)
    {
        return false;
    }

    /**
     * 是否把值转化为时间戳
     *
     * @return static
     */
    public function time()
    {
        $this->toTimestamp = true;

        return $this;
    }
}
