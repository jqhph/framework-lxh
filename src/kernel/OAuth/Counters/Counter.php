<?php

namespace Lxh\OAuth\Counters;

use Lxh\Cache\Cache;
use Lxh\OAuth\User;

class Counter
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $prefix = 'cou_';

    public function __construct(User $user)
    {
        $this->user  = $user;
        $this->cache = $user->cache();
    }

    public function incr($username)
    {
        return $this->cache->set(
            $this->normalize($username),
            $this->total($username) + 1,
            $this->user->option('fail-interval') ?: 600
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
