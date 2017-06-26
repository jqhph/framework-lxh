<?php
/**
 * 集合
 *
 * @author Jqh
 * @date   2017/6/13 19:13
 */

namespace Lxh\Helper;

class Entity
{
    /**
     * 属性
     *
     * @var array
     */
    protected $attrs = [];

    /**
     * 属性回收站
     *
     * @var array
     */
    protected $recycled = [];

    public function __construct(array $data = [])
    {
        $this->attrs = $data;
    }

    // 回收数据
    public function recycle($name)
    {
        $this->recycled[$name] = $this->attrs[$name];
        unset($this->attrs[$name]);
    }

    // 删除数据
    public function remove($name)
    {
        unset($this->attrs[$name]);
    }

    // 判断属性是否存在
    public function has($name)
    {
        return isset($this->attrs[$name]);
    }

    // 注入属性
    public function fill(array $data)
    {
        $this->attrs = array_merge($this->attrs, $data);
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
        if (empty($key)) {
            return $this->attrs;
        }

        $lastItem = & $this->attrs;
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
        $this->attrs[$name] = $value;
        return $this;
    }

    // 追加值到某一属性中
    public function append($name, $value)
    {
        $this->attrs[$name][] = $value;
        return $this;
    }

    // 清除属性
    public function reset()
    {
        $this->attrs = [];
    }

    public function all()
    {
        return $this->attrs;
    }

    public function toArray()
    {
        return $this->attrs;
    }

    public function toJson()
    {
        return json_encode($this->attrs);
    }

    // 获取属性值
    public function __get($name)
    {
        return isset($this->attrs[$name]) ? $this->attrs[$name] : null;
    }

    // 设置属性值
    public function __set($name, $value)
    {
        $this->attrs[$name] = $value;
    }

    public function __toString()
    {
        return json_encode($this->attrs);
    }
}
