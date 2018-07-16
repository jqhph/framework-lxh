<?php

namespace Lxh\Coroutine;

/**
 * 迭代生成器返回值代理类
 *
 * Class Value
 * @package Lxh\Coroutine
 */
class Value
{
    protected $value;

    public function __construct($value)
    {
        $this->value = &$value;
    }

    public function get()
    {
        return $this->value;
    }
}
