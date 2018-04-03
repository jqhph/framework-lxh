<?php

namespace Lxh\OperationsLogger\Entities;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Jsonable;

/**
 * 日志格式定义类
 *
 * @author Jqh
 * @date   2018/4/2 17:40
 */
abstract class Entity implements Arrayable, Jsonable
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $k => &$v) {
            $this->$k = $v;
        }
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return array
     */
    abstract public function toArray();

    /**
     * 保存日志
     *
     * @return bool
     */
    abstract public function add();
}
