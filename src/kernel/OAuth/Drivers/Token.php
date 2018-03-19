<?php

namespace Lxh\OAuth\Drivers;

use Lxh\OAuth\Database\User;

class Token extends Driver
{
    public function save(User $user, $remember)
    {
        return $this->user->cache()->set(
            $this->normalizeKey($this->user->logs()->item('token')),
            $user->toArray(),
            $this->user->getLife($remember)
        );
    }

    protected function normalizeKey($key)
    {
        return 't_' . $key;
    }

    public function check()
    {
        $token = I($this->user->option('tokenKey') ?: 'access_token');

        if (! $token) {
            return false;
        }

        // 如果token存在，则允许登录
        if (
            ! $data = $this->user->cache()->get($this->normalizeKey($token))
        ) {
            return false;
        }

        $user = $this->user->model();

        $user->attach($data);

        return true;

    }

    public function getEncryptTarget(User $user)
    {
        return $user->getId();
    }

    /**
     * 登出操作
     *
     * @return bool
     */
    public function logout()
    {
        // 登陆日志状态改为无效
        $this->user->logs()->logout();
        // 清除缓存
        $this->user->cache()->delete(
            $this->normalizeKey($this->user->model()->logs('token'))
        );
    }

}
