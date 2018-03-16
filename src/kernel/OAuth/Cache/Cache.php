<?php

namespace Lxh\OAuth\Cache;

abstract class Cache
{
    /**
     * 缓存驱动对象
     *
     * @var mixed
     */
    protected $driver;

    /**
     *
     * @var string
     */
    protected $prefix = 'lxh_';

    /**
     * 保存数据到缓存
     *
     * @param string $key
     * @param mixed $value
     * @param int $life 缓存时长，秒
     * @return bool
     */
    abstract function set($key, $value, $life = 0);

    /**
     * 获取缓存数据
     *
     * @param string $key
     * @return mixed
     */
    abstract function get($key);

    /**
     * 清除缓存
     *
     * @param $key
     * @return bool
     */
    abstract function delete($key);

    /**
     * 追加数据到缓存
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    abstract public function append($key, $value);

    /**
     * 获取追加的缓存数据
     * 返回一个数组
     *
     * @param $key
     * @return array
     */
    abstract public function getArray($key);

    /**
     * 从删除一个值
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    abstract public function deleteInArray($key, $value);

    /**
     * 获取保存的key
     *
     * @param $key
     * @return bool|string
     */
    protected function normalizeKey($key)
    {
        return $this->prefix . $key;
    }

    /**
     * 获取前缀
     *
     * @return string
     */
    public function prefix()
    {
        return $this->prefix;
    }

    /**
     * 设置前缀
     *
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
