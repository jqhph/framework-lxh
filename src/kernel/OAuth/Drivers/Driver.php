<?php

namespace Lxh\OAuth\Drivers;

use Lxh\MVC\Model;
use Lxh\OAuth\User;
use Lxh\OAuth\Database;

abstract class Driver
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 保存用户登录数据
     *
     * @param Database\User $user
     * @param $remember
     * @return mixed
     */
    abstract public function save(Database\User $user, $remember);

    /**
     * 检查用户是否已登录
     *
     * @return false|array
     */
    abstract public function check();

    /**
     * 获取加密目标
     *
     * @param Database\User $user
     * @return mixed|string
     */
    abstract public function getEncryptTarget(Database\User $user);

    /**
     * 登出方法
     *
     * @return mixed
     */
    abstract public function logout();

}