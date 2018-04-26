<?php

namespace Lxh\RequestAuth\Counters;

use Lxh\Cache\CacheInterface;
use Lxh\RequestAuth\Auth;

class Counter
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $prefix = 'cou_';

    public function __construct(Auth $auth)
    {
        $this->auth  = $auth;
        $this->cache = $auth->cache();
    }

    public function incr($username)
    {
        return $this->cache->set(
            $this->normalize($username),
            $this->total($username) + 1,
            $this->auth->option('reject-interval') ?: 600
        );
    }

    public function reset($username)
    {
        return $this->cache->delete($this->normalize($username));
    }

    /**
     * 获取总次数
     *
     * @param $username
     * @return int
     */
    public function total($username)
    {
        return $this->cache->get($this->normalize($username)) ?: 0;
    }

    protected function normalize($username)
    {
        return $this->prefix . $username;
    }

}
