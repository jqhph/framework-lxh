<?php
/**
 * 缓存抽象类
 *
 * @author Jqh
 * @date   2017/8/11 10:52
 */

namespace Lxh\Kernel\Cache;

abstract class Cache
{
    /**
     * 保存缓存
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $timeout 设置缓存$timeout秒后过期，0为不过期
     * @return bool
     */
    abstract public function set($key, $value, $timeout = 0);

    /**
     * 获取缓存内容
     *
     * @param  string $key
     * @return mixed 内容过期或不存在返回false
     */
    abstract public function get($key);

    /**
     * 删除缓存
     *
     * @param string $key
     * @return bool
     */
    abstract public function delete($key);

    /**
     * 设置缓存n秒后过期
     *
     * @param  string $key
     * @param  int    $timeout 设置缓存在$timeout秒后过期
     * @return bool
     */
    abstract public function expiresAfter($key, $timeout);


    /**
     * 设置缓存过期时间
     *
     * @param  string $key
     * @param  int    $expires 设置缓存在某一时间点过期，用时间戳格式，0为不过期
     * @return bool
     */
    abstract public function expiresAt($key, $expires);

    public function reset()
    {

    }
}
