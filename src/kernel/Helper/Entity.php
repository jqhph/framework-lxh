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
use ArrayAccess;

class Entity implements ArrayAccess, Arrayable, Jsonable
{
    /**
     * 属性
     *
     * @var array
     */
    protected $items = [];

    public function __construct(array $data = [])
    {
        $this->items = &$data;
    }

    /**
     * 删除数据
     *
     * @param string $name
     * @return bool
     */
    public function remove($name)
    {
        Util::unsetInArray($this->items, $name);

        return true;
    }

    /**
     * 判断属性是否存在
     * 支持使用 “attrs.name”判断多维数组key是否存在
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->items[$name])) return true;

        $lastItem = & $this->items;
        foreach (explode('.', $name) as & $keyName) {
            if (isset($lastItem[$keyName])) {
                $lastItem = & $lastItem[$keyName];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * 注入属性
     *
     * @param array $data
     * @return static
     */
    public function fill(array $data)
    {
        $this->items = array_merge($this->items, $data);

        return $this;
    }

    public function attach(array $data)
    {
        $this->items = array_merge($this->items, $data);

        return $this;
    }

    /**
     * 获取属性值，可获取多维属性值
     *
     * @param  $name string
     * @param  $default mixed 默认值
     * @return mixed
     */
    public function get($key, $default = null)
    {
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

    /**
     * 设置属性值
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function set($name, $value)
    {
        $this->items[$name] = & $value;
        return $this;
    }

    /**
     * 追加值到某一属性中
     *
     * @return static
     */
    public function append($name, $k, $v = null)
    {
        if ($v === null) {
            $this->items[$name][] = &$k;
        } else {
            $this->items[$name][$k] = &$v;
        }

        return $this;
    }

    /**
     * 清除属性
     *
     * @return void
     */
    public function reset()
    {
        $this->items = [];
    }

    /**
     * 获取所有属性
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * 以数组形式返回所有属性
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * 返回json格式所有属性数据
     *
     * @param mixed $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items);
    }

    /**
     * 获取属性值
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * 设置属性值
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->items[$name] = &$value;
    }

    public function __toString()
    {
        return json_encode($this->items);
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->items[$key] = &$value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }
}
