<?php
/**
 * 实体
 *
 * @author Jqh
 * @date   2017/6/13 19:13
 */

namespace Lxh\Helper;

use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Jsonable;

class Entity implements Arrayable, Jsonable
{
    /**
     * 属性
     *
     * @var array
     */
    protected $items = [];

    /**
     * 属性回收站
     *
     * @var array
     */
    protected $recycled = [];

    public function __construct(array $data = [])
    {
        $this->items = $data;
    }

    // 回收数据
    public function recycle($name)
    {
        $this->recycled[$name] = $this->items[$name];
        unset($this->items[$name]);
    }

    // 删除数据
    public function remove($name)
    {
        unset($this->items[$name]);
    }

    // 判断属性是否存在
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    // 注入属性
    public function fill(array $data)
    {
        $this->items = array_merge($this->items, $data);
    }

    /**
     * 获取属性值，可获取多维属性值
     *
     * @param  $name string
     * @param  $default mixed 默认值
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->items;
        }

        if (isset($this->items[$key])) return $this->items[$key];

        $lastItem = & $this->items;
        foreach (explode('.', $key) as & $keyName) {
            if (isset($lastItem[$keyName])) {
                $lastItem = & $lastItem[$keyName];
            } else {
                return $default;
            }
        }

        return $lastItem;
    }

    // 设置属性值
    public function set($name, $value)
    {
        $this->items[$name] = $value;
        return $this;
    }

    // 追加值到某一属性中
    public function append($name, $value)
    {
        $this->items[$name][] = $value;
        return $this;
    }

    // 清除属性
    public function reset()
    {
        $this->items = [];
    }

    public function all()
    {
        return $this->items;
    }

    public function toArray()
    {
        return $this->items;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->items);
    }

    // 获取属性值
    public function __get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    // 设置属性值
    public function __set($name, $value)
    {
        $this->items[$name] = $value;
    }

    public function __toString()
    {
        return json_encode($this->items);
    }
}
