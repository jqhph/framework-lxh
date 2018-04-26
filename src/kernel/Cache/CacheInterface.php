<?php

namespace Lxh\Cache;

interface CacheInterface
{
    /**
     * 设置缓存
     *
     * @param $key
     * @param $content
     * @param int $timeout
     * @return bool
     */
    public function set($key, $content, $timeout = 0);

    /**
     * 获取缓存
     *
     * @param string $key
     * @return mixed 内容过期或不存在返回false
     */
    public function get($key);

    /**
     * 设置数组缓存
     *
     * @param $key
     * @param array $content
     * @param int $timeout
     * @return bool
     */
    public function setArray($key, array $content, $timeout = 0);

    /**
     * 获取数组缓存
     *
     * @param string $key
     * @return array 内容过期或不存在返回false
     */
    public function getArray($key);

    /**
     * 追加数据到缓存
     *
     * @param string $key
     * @param string $value
     * @param int $timeout
     * @return bool
     */
    public function appendInArray($key, $value, $timeout = 0);

    /**
     * 删除数组中的值
     *
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     */
    public function deleteInArray($key, $value, $timeout = 0);

    /**
     * 确认缓存项的检查是否命中。
     *
     * 注意: 调用此方法和调用 `get()` 时 **一定不可** 有先后顺序之分。
     *
     * @return bool
     *   如果缓冲池里有命中的话，返回 `true`，反之返回 `false`
     */
    public function exist($key);

    /**
     * 删除一个缓存
     *
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * 设置缓存过期时间
     *
     * @param $key
     * @param $date
     * @return mixed
     */
    public function expiresAt($key, $date);

    /**
     * 设置缓存n秒后过期
     *
     * @param $key
     * @param $time
     * @return mixed
     */
    public function expiresAfter($key, $time);

    /**
     * 自增1
     *
     * @param $key
     * @param int $timeout
     * @return mixed
     */
    public function incr($key, $timeout = 0);

    /**
     * 自减1
     *
     * @param $key
     * @param int $timeout
     * @return mixed
     */
    public function decr($key, $timeout = 0);

    /**
     * 清除整个库下的缓存
     *
     * @param string $type
     * @return mixed
     */
    public function flush($type = null);
}
