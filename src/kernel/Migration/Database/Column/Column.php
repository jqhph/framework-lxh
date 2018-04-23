<?php

namespace Lxh\Migration\Database\Column;

/**
 *
 * @method Column default($value) 设置默认值
 */
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

    public function __construct($name)
    {
        $this->name = $name;
    }

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

    /**
     * 设置选项值
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = &$value;
        return $this;
    }

    public function deleteOption($key)
    {
        unset($this->options[$key]);
        return $this;
    }

    /**
     * 指定字段放置在哪个字段后面
     *
     * @param string $name 字段名
     * @return Column
     */
    public function after($name)
    {
        return $this->setOption('after', $name);
    }

    /**
     * 字段注释
     *
     * @param string $comment
     * @return Column
     */
    public function comment($comment)
    {
        return $this->setOption('comment', $comment);
    }

    /**
     * @param int $len
     * @return $this
     */
    public function limit($len)
    {
        return $this->setOption('limit', $len);
    }

    /**
     * @param $len
     * @return $this
     */
    public function length($len)
    {
        return $this->limit($len);
    }

    /**
     * 允许字段值为null
     *
     * @return Column
     */
    public function null()
    {
        $this->deleteOption('default');
        return $this->setOption('null', true);
    }

    /**
     * 不允许字段值为null
     *
     * @return Column
     */
    public function notNull()
    {
        return $this->setOption('null', false);
    }

    public function __call($name, $arguments)
    {
        if ($name == 'default') {
            return $this->setOption('default', get_value($arguments, 0));
        }
    }

}
