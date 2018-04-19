<?php

namespace Lxh\Migration\Database\Column;

abstract class Column
{
    /**
     * 字段名称
     *
     * @var string
     */
    protected $name;

    /**
     * 字段类型
     *
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options = [];

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getOptions()
    {
        return $this->options;
    }

}
