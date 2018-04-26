<?php

namespace Lxh\RequestAuth\Cache;

use Lxh\Cache\CacheInterface;
use Lxh\RequestAuth\Entities\Log;

class TokenCache
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * 缓存key前缀
     *
     * @var string
     */
    protected $prefix = 'tkc_';

    /**
     * 缓存key额外条件
     *
     * @var string
     */
    protected $condition;

    public function __construct(CacheInterface $cache, $condition = '')
    {
        $this->cache     = $cache;
        $this->condition = $condition;
    }

    public function setEncryptCode($key, $value, $lifetime)
    {
        return $this->cache->set('ek_'.$key, $value, $lifetime);
    }

    public function getEncryptCode($key)
    {
        return $this->cache->get('ek_'.$key);
    }

    public function deleteEncryptCode($key)
    {
        return $this->cache->delete('ek_'.$key);
    }

    /**
     * 缓存所有选项
     *
     * @param int $userId
     * @param string $token
     * @param array $log
     * @param int $life 缓存有效期（秒）
     */
    public function setForUserIdAndToken($userId, $token, array $log, $life)
    {
        // 缓存登录日志
        $this->cache->setArray(
            $this->normalizeItemsKey($userId, $token),
            $log,
            $life + 5
        );

    }

    /**
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function getForUserIdAndToken($userId, $token)
    {
        return $this->cache->getArray($this->normalizeItemsKey($userId, $token));
    }

    /**
     * 移除登录日志信息
     *
     * @param int $userId
     * @param int $token
     */
    public function deleteForUserIdAndToken($userId, $token)
    {
        $this->cache->delete(
            $this->normalizeItemsKey($userId, $token)
        );
    }

    /**
     * 格式化登陆日志key值
     *
     * @param int $userId
     * @param string $token
     * @return string
     */
    protected function normalizeItemsKey($userId, $token)
    {
        return $this->formatKey($userId.'_'.$token);
    }

    /**
     * 判断session模式下token是否可用
     *
     * @return mixed
     */
    public function getForToken($token)
    {
        return $this->cache->get($this->formatKey($token));
    }

    /**
     * 此缓存有值，表示token可用
     * 当用户登出，或者用户被提出时，此缓存会被移除
     *
     * @param $token
     * @param $lifetime
     */
    public function setActiveForToken($token, $lifetime)
    {
        $this->cache->set($this->formatKey($token), 1, $lifetime + 10);
    }

    /**
     * 移除token可用标识
     *
     * @param string $token
     */
    public function deleteForToken($token)
    {
        $this->cache->delete($this->formatKey($token));
    }

    /**
     * 缓存所有用户登录的token和过期时间，根据用户id作为key
     * 登录时可以根据此缓存判断用户是否已登录过
     *
     * @param int $userId
     */
    public function appendForUserId($userId, Log $log)
    {
        $end = $log->life + $log->created_at;

        $this->cache->appendInArray(
            $this->formatKey($userId),
            "{$log->id}-{$log->token}-{$log->app}-{$end}"
        );
    }

    /**
     * 根据用户id获取所有登录token内容
     *
     * @param $userId
     * @return array
     */
    public function getArrayForUserId($userId)
    {
        return $this->cache->getArray($this->formatKey($userId));
    }

    public function setArrayForUserId($userId, array $data)
    {
        return $this->cache->setArray($this->formatKey($userId), $data);
    }

    /**
     * 移除指定token的以用户id为键的缓存
     *
     * @param int $userId
     * @param string $token
     */
    public function deleteInArrayKeyByUserIdWhenEqToken($userId, $token)
    {
        $key = $this->formatKey($userId);

        if ($data = $this->cache->getArray($key)) {
            foreach ($data as $k => &$v) {
                if (strpos($v, $token) !== false) {
                    unset($data[$k]);
                    break;
                }
            }
            if ($data) {
                $this->cache->setArray($key, $data);
            } else {
                $this->cache->delete($key);
            }
        }
    }

    /**
     * 格式化缓存key
     *
     * @param $key
     * @return string
     */
    protected function formatKey($key)
    {
        return $this->prefix . $key . $this->condition;
    }

    public function parseUserIdValue($value)
    {
        return explode('-', $value);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->cache, $name], $arguments);
    }
}
