<?php

namespace Lxh\OperationsLogger\Entities;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Jsonable;
use Lxh\MVC\Model;

/**
 * 日志格式定义类
 *
 * @author Jqh
 * @date   2018/4/2 17:40
 */
abstract class Entity implements Arrayable, Jsonable
{
    /**
     * @var Model
     */
    protected $entityModel = null;

    /**
     * @var bool
     */
    protected $enable = false;

    public function __construct(Model $model = null)
    {
        $this->entityModel = $model;
        $this->enable      = config('admin.use-operations-log', false);
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
