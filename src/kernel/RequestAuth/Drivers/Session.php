<?php

namespace Lxh\RequestAuth\Drivers;

use Lxh\RequestAuth\Exceptions\AuthTokenException;
use Lxh\RequestAuth\Exceptions\EncryptCodeException;
use Lxh\RequestAuth\Exceptions\UserNotExistException;

class Session extends Driver
{
    /**
     * cookie和session key值
     *
     * @var string
     */
    protected $key = '_ses_';

    public function logged($remember = false)
    {
        // 使用session保存
        if ($remember) {
            $token = $this->token->get();

            // 使用cookie保存token
            $value = $this->normalizeCookieValue($token);

            // 缓存时间
            $life = $this->auth->getLifetime();

            // 保存到cookie
            cookie()->save($this->key, $value, $life);
        }

        return session()->save($this->key, $this->user->toArray());
    }

    /**
     * 检查session
     *
     * @return bool
     * @throws AuthTokenException
     */
    protected function checkSession()
    {
        // 使用session保存
        if ($data = session()->get($this->key)) {
            $this->auth->attachToUser($data);

            $log = $this->user->log();
            $this->token->setLog($log);
            $token = $log->token;

            if (
                ! $this->token->isTokenEffective($token)
            ) {
                $this->dump($token);
                // token已失效，需要重新登录
                throw new AuthTokenException('token失效，用户可能在其他设备登录！');
            }

            return true;
        }
    }

    protected function checkCookie()
    {
        // 检查cookie中是否存在登录数据
        if (! $result = cookie()->get($this->key)) {
            return false;
        }

        list($userId, $token) = $this->parseCookieValue($result);

        $this->auth->setUserId($userId);

        if (
        ! $this->token->isTokenEffective($token)
        ) {
            $this->dump($token);
            // token已失效，需要重新登录
            throw new AuthTokenException('token失效，用户可能在其他设备登录');
        }

        // 返回false表示token失效或过期
        if (!$code = $this->token->findEncryptCode($token)) {
            $this->dump($token);
            // token已失效，需要重新登录
            throw new EncryptCodeException('token失效，获取token加密随机码失败');
        }

        // 验证token是否正确
        if (! $this->token->check($token, $code)) {
            $this->dump($token);

            return false;
        }

        // 获取登录日志数据
        $log = $this->token->find($token);

        // 查找用户数据
        $userData = $this->user->findForLogged();
        if (! $userData) {
            $this->dump($token);
            throw new UserNotExistException('用户不存在或未激活');
        }

        $this->user->attach($userData);
        $this->user->setLog($log);

        // 保存到session
        session()->save($this->key, $this->user->toArray());

        return true;


    }

    /**
     * 检查用户是否已登录
     *
     * @return bool
     */
    public function check()
    {
        $result = $this->checkSession();
        if (is_bool($result)) {
            return $result;
        }

        return $this->checkCookie();
    }

    public function logout()
    {
        $log = $this->user->log();

        $this->token->setInactiveByUser($log->token, $log->id);
    }

    /**
     * 登出操作
     *
     * @param $userId
     * @param $token
     * @return void
     */
    public function dump($token)
    {
        // 登陆日志状态改为无效
        $this->token->setInactive($token);

        session()->delete($this->key);
        cookie()->delete($this->key);
    }

}
