<?php

namespace Lxh\RequestAuth\Database;

use Lxh\RequestAuth\Entities\Log;
use Lxh\RequestAuth\Exceptions\UserIdNotFoundException;

trait FindLog
{
    /**
     * @var Log
     */
    protected $log;

    /**
     * 获取登陆日志数据
     *
     * @param mixed $token
     * @return Log
     */
    public function find($token)
    {
        $userId = $this->user->getId();

        if (!$userId) {
            throw new UserIdNotFoundException;
        }

        if (! $this->log && $token) {
            $this->log = new Log((array)$this->cache->getForUserIdAndToken($userId, $token));
        }

        if ($this->storable && ! $this->log && $token) {
            $log = $this->model()
                    ->select('*')
                    ->where(['user_id' => $userId, 'token' => &$token])
                    ->findOne();

            $this->log = new Log($log);
        }

        $this->token = $this->log->token;

        return $this->log;
    }

    /**
     * 获取cookie加密随机码
     *
     * @param $token
     * @return string
     */
    public function findEncryptCode($token)
    {
        $key = $this->cache->getEncryptCode(
            $this->auth->driver()->normalizeCookieValue($token)
        );

        if ($key) {
            return $key;
        }

        // 缓存中不存在，从数据库中查找
        $this->find($token);

        if (!$this->isActive()) {
            return false;
        }

        return $this->log->key;
    }

    /**
     * 从数据库中查找为过期的token
     *
     * @param $userId
     * @return array
     */
    protected function findActivesTokenInDatabase($userId)
    {
        if (!$data = $this->findActivesForUserId($userId)) {
            return false;
        }

        $time = time();

        $inactives = [];
        $actives = [];
        // 判断日志是否过期
        foreach ($data as $k => &$v) {
            if ($v['life'] + $v['created_at'] < $time) {
                $inactives[] = $v['id'];
                unset($data[$k]);
            } else {
                $actives[] = ['id' => $v['id'], 'token' => $v['token'], 'app' => $v['app'], 'user_id' => $userId];
            }
        }
        
        if ($inactives) {
            $this->updateInactiveForIds($inactives);
        }

        return $actives;

    }

    protected function findActivesForUserId($userId)
    {
        return $this->model()
            ->select('*')
            ->where(['user_id' => $userId, 'active' => 1])
            ->find();
    }


    /**
     * 获取最新登录日志
     *
     * @return array
     */
    public function findActiveLatestLoginedLog()
    {
        $userId = $this->user->getId();

        if (! $userId) return [];

        return $this->model()
//            ->select('life,token,id,created_at,device,ip')
            ->where(
                [
                    'user_id' => $userId,
                    'active'  => 1,
                    'app'     => $this->auth->app(),
                    'type'    => $this->getType()
                ]
            )
            ->sort('id DESC')
            ->findOne();
    }

}
