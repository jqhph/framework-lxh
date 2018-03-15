<?php

namespace Lxh\OAuth\Database;

use Lxh\MVC\Model;

abstract class User extends Model
{
    /**
     * 用户登录方法
     *
     * @param string $username
     * @param string $password
     * @return array|false
     */
    abstract public function login($username, $password);

    /**
     * 获取需要保存到登录缓存中的用户数据
     *
     * @return array|false
     */
    abstract public function findForLogined();

}
