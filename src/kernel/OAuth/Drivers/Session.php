<?php

namespace Lxh\OAuth\Drivers;

use Lxh\Exceptions\Exception;
use Lxh\MVC\Model;
use Lxh\OAuth\Cache\File;
use Lxh\OAuth\Exceptions\AuthTokenException;
use Lxh\OAuth\Exceptions\EncryptCodeException;
use Lxh\OAuth\Exceptions\UserNotExistEception;
use Lxh\OAuth\User;
use Lxh\OAuth\Database;

class Session extends Driver
{
    /**
     * @var User
     */
    protected $user;

    protected $key = '_ses_';

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 保存数据到缓存
     *
     * @param Database\User $user
     * @param bool $remember
     * @return bool
     */
    public function save(Database\User $user, $remember)
    {
        // 使用session保存
        if ($remember) {
            // 使用cookie保存token
            $value = $this->normalizeValue($this->getEncryptTarget($user), $user->logs('token'));

            // 缓存时间
            $life = $this->user->getLife($remember);

            // 保存到cookie
            cookie()->save($this->key, $value, $life);

            $cache = $this->user->cache();
            if (! $cache instanceof File) {
                // 如果用的不是文件缓存，则保存到缓存
                $cache->set($value, $user->logs('key'), $life + 10);
            }

        }

        return session()->save($this->key, $user->toArray());

    }

    protected function normalizeValue($uid, $token)
    {
        return $uid.'_'.$token;
    }

    protected function checkSession()
    {
        $model = $this->user->model();
        // 使用session保存
        if ($data = session()->get($this->key)) {
            $model->attach($data);

            $this->user->setToken($token = $model->logs('token'));

            if (
            ! $this->user->logs()->isTokenActiveForSession($token)
            ) {
                $this->logout();
                // token已失效，需要重新登录
                throw new AuthTokenException('token失效，用户可能在其他设备登录！');
            }

            return true;
        }
    }

    protected function checkCookie()
    {
        $model = $this->user->model();

        // 检查cookie中是否存在登录数据
        if (! $result = cookie()->get($this->key)) {
            return false;
        }

        list($uid, $token) = explode('_', $result);

        $model->setId($uid);

        $this->user->setToken($token);

        if (
        ! $this->user->logs()->isTokenActiveForSession($token)
        ) {
            $this->logout();
            // token已失效，需要重新登录
            throw new AuthTokenException('token失效，用户可能在其他设备登录');
        }

        // 返回false表示token失效或过期
        if (!$code = $this->findEncryptCode($uid, $token)) {
            $this->logout();
            // token已失效，需要重新登录
            throw new EncryptCodeException('token失效，获取token加密随机码失败');
        }

        // 验证token是否正确
        if (! $this->user->vertifyToken($token, $model, $code)) {
            $this->logout();
            
            return false;
        }

        // 获取登录日志数据
        $logs = $this->user->logs()->find($uid, $token);

        // 查找用户数据
        $userData = $model->findForLogined();
        if (! $userData) {
            $this->logout();
            throw new UserNotExistEception('用户不存在或未激活');
        }

        $model->attach($userData);
        $model->setLogs($logs);

        // 保存到session
        session()->save($this->key, $model->toArray());

        return true;


    }

    /**
     * 检查用户是否已登录
     *
     * @return false|array
     */
    public function check()
    {
        $result = $this->checkSession();
        if (is_bool($result)) {
            return $result;
        }

        return $this->checkCookie();
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

        session()->delete($this->key);
        cookie()->delete($this->key);
    }

    /**
     * 查找加密随机码
     *
     * @param $id
     * @param $token
     */
    protected function findEncryptCode($uid, $token)
    {
        $cache = $this->user->cache();
        if (! $cache instanceof File) {
            if ($code = $cache->get($this->normalizeValue($uid, $token))) {
                return $code;
            }
        }
        // 从数据库中查找
        return $this->user->logs()->findEncryptCode($uid, $token);

    }

    /**
     * 获取加密目标
     *
     * @param Database\User $user
     * @return mixed|string
     */
    public function getEncryptTarget(Database\User $user)
    {
        return $user->getId();
    }

}
