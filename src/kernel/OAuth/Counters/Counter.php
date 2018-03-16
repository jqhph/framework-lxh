<?php

namespace Lxh\OAuth;

class Counter
{
    /**
     * @var User
     */
    protected $user;

    /**
     *
     * @var int
     */
    protected $total = 0;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $prefix = 'cou_';

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function add($key)
    {
        $this->total ++;

        $this->user->cache()->set();
    }

    public function total()
    {
        return $this->total;
    }
}
