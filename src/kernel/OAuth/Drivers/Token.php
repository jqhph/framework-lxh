<?php

namespace Lxh\OAuth\Drivers;

use Lxh\OAuth\Database\User;

class Token extends Driver
{
    public function save(User $user, $remember)
    {
        return $this->user->cache()->set(
            $this->user->logs()->item('token'), $user->toArray(), $this->user->getLife($remember)
        );
    }

    public function check()
    {
    }

    public function getEncryptTarget(User $user)
    {
    }

    /**
     * 登出操作
     *
     * @return bool
     */
    public function logout()
    {
        // 登陆日志状态改为无效
        $this->user->logs()->inactive();
    }

}
