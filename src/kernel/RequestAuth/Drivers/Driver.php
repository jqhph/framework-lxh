<?php

namespace Lxh\RequestAuth\Drivers;

use Lxh\RequestAuth\Auth;
use Lxh\RequestAuth\Database\User;
use Lxh\RequestAuth\Exceptions\UserIdNotFoundException;
use Lxh\RequestAuth\Token;

abstract class Driver
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Token
     */
    protected $token;

    public function __construct(Auth $auth)
    {
        $this->auth  = $auth;
        $this->user  = $auth->user();
        $this->token = $auth->token();
    }

    /**
     * 用户主动注销登录状态
     *
     * @param $userId
     * @param $token
     * @param $logId
     * @return mixed
     */
    public function setInactiveByUser($token, $logId = 0)
    {
        return $this->auth->token()->setInactiveByUser($token, $logId);
    }

    /**
     * 把token状态设置为无效
     *
     * @param $userId
     * @param $logId
     * @param $token
     * @return mixed
     */
    public function setInactive($token, $logId = 0)
    {
        return $this->auth->token()->setInactive($token, $logId);
    }

    /**
     * 获取需要加密的字符串
     *
     * @return string
     */
    public function getEncryptTarget()
    {
        $id = $this->user->getId();

        if (! $id) {
            throw new UserIdNotFoundException;
        }

        return $id.'~'.$this->user->getEncryptType();
    }

    /**
     * 检查用户是否已登录
     *
     * @return false|array
     */
    abstract public function check();

    /**
     * 登出操作
     *
     * @return mixed
     */
    abstract public function logout();

    /**
     * 用户登录成功后置操作
     *
     * @param bool $remember
     * @return mixed
     */
    abstract public function logged($remember = false);

    /**
     * 缓存到cookie的值
     *
     * @param $token
     * @return string
     */
    public function normalizeCookieValue($token)
    {
        return $this->getEncryptTarget().'_'.$token;
    }

    /**
     * 解析cookie保存的值
     *
     * @param $value
     * @return array
     */
    protected function parseCookieValue($value)
    {
        return explode(
            '_', str_replace('~'.$this->user->getEncryptType(), '', $value)
        );
    }

}
