<?php

namespace Lxh\OAuth\Drivers;

use Lxh\Exceptions\Exception;
use Lxh\MVC\Model;
use Lxh\OAuth\Cache\File;
use Lxh\OAuth\Exceptions\AuthTokenException;
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
            $value = $this->normalize($user->get('logs.token'), $this->getEncryptTarget($user));

            // 缓存时间
            $life = $this->user->getLife($remember);

            // 保存到cookie
            cookie()->save($this->key, $value, $life);

            $cache = $this->user->cache();
            if (! $cache instanceof File) {
                // 如果用的不是文件缓存，则保存到缓存
                $cache->set($value, $user->get('logs.key'), $life + 10);
            }

        }

        return session()->save($this->key, $user->toArray());

    }
    
    

    protected function normalize($id, $token)
    {
        return $id.'_'.$token;
    }

    /**
     * 检查用户是否已登录
     *
     * @return false|array
     */
    public function check()
    {
        // 使用session保存
        if ($data = session()->get($this->key)) {
            $this->user->model()->attach($data);

            return true;
        }

        // 检查cookie中是否存在登录数据
        if (! $result = cookie()->get($this->key)) {
            return false;
        }

        $model = $this->user->model();

        list($id, $token) = explode(',', $result);

        $model->setId($id);

        $code = $this->findEncryptCode($id, $token);

        // 验证token是否正确
        if (! $this->user->vertifyToken($token, $this->getEncryptTarget($model), $code)) {
            return false;
        }

        $logs = $this->user->logs()->find($id, $token);

        // 查找用户数据
        $userData = $model->findForLogined();
        if (! $userData) {
            return false;
        }

        $userData['logs'] = &$logs;

        // 保存到cookie
        session()->save($this->key, $userData);

        $model->attach($userData);

        return true;
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

        session()->delete($this->key);
        cookie()->delete($this->key);
    }

    /**
     * 查找加密随机码
     *
     * @param $id
     * @param $token
     */
    protected function findEncryptCode($id, $token)
    {
        $cache = $this->user->cache();
        if (! $cache instanceof File) {
            if ($code = $cache->get($this->normalize($id, $token))) {
                return $code;
            }
        }
        // 从数据库中查找
        return $this->user->logs()->findEncryptCode($id, $token);

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
