<?php

namespace Lxh\Admin\Data;

/**
 * 保存行数据
 */
class Items
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var int
     */
    protected $offset = 0;

    public function __construct(array $items, $offset = 0)
    {
        $this->items = &$items;
        $this->offset = $offset;
    }

    /**
     * 获取当前行某一列
     *
     * @param $key
     * @param null $def
     * @return mixed|null
     */
    public function get($key, $def = null)
    {
        return isset($this->items[$key]) ? $this->items[$key] : $def;
    }

    /**
     * 获取当前行某一列
     *
     * @param $key
     * @param null $def
     * @return mixed|null
     */
    public function column($key, $def = null)
    {
        return isset($this->items[$key]) ? $this->items[$key] : $def;
    }

    /**
     * @return int
     */
    public function offset()
    {
        return $this->offset;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->items);
    }

}
