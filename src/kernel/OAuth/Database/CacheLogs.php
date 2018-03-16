<?php

namespace Lxh\OAuth\Database;

use Lxh\OAuth\Cache\Cache;

trait CacheLogs
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * 判断是否可以缓存登录日志
     *
     * @return bool
     */
    protected function useCache()
    {
        return $this->isOpen || !$this->saveable;
    }

    /**
     * 判断session模式下token是否可用
     *
     * @return mixed
     */
    public function isTokenActiveForSession($token)
    {
        return $this->cache->get($token);
    }

    protected function saveCache($uid)
    {
        $cache = $this->cache;

        $this->saveItemsInCache();

        // 缓存所有用户登录的token和过期时间，根据用户id作为key
        // 登录时可以根据此缓存判断用户是否已登录过
        $cache->append($uid, $this->normalizeUserIdValue());

        if (!$this->isOpen) {
            // 此缓存有值，表示token可用
            // 当用户登出，或者用户被提出时，此缓存会被移除
            $cache->set($this->items['token'], 1);
        }
    }

    protected function saveItemsInCache()
    {
        if ($this->useCache()) {
            // 缓存登录日志
            $this->cache->set(
                $this->normalizeKey($this->item('user_id'), $this->items['token']),
                $this->items,
                $this->items['life'] + 5
            );
        }
    }

    protected function deleteItemsInCache($userId = null, $token = null)
    {
        if ($this->useCache()) {
            $this->cache->delete(
                $this->normalizeKey(
                    $userId ?:$this->item('user_id'),
                    $token ?: $this->item('token')
                )
            );
        }
    }

    protected function deleteForUserId($uid = null, $token = null)
    {
        $uid   = $uid ?: $this->item('user_id');
        $token = $token ?: $this->item('token');
        if ($data = $this->cache->getArray($uid)) {
            foreach ($data as $k => &$v) {
                if (strpos($v, $token) === 0) {
                    unset($data[$k]);
                    break;
                }
            }
            $this->cache->set($uid, $data);
        }
    }

    protected function deleteForToken($token = null)
    {
        if (!$this->isOpen) {
            $this->cache->delete($token ?: $this->item('token'));
        }
    }

    protected function normalizeUserIdValue()
    {
        return $this->items['id'] . '-'
        . $this->items['token'] . '-'
        . $this->items['entry']
        . ($this->items['life'] + $this->items['created_at']);
    }

    protected function parseUserIdValue($value)
    {
        return explode('-', $value);
    }
}