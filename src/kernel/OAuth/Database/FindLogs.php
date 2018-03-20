<?php

namespace Lxh\OAuth\Database;

trait FindLogs
{
    /**
     * 保存多条登录日志
     *
     * @var array
     */
    protected $rows = [];

    /**
     * 获取登陆日志数据
     *
     * @param mixed $uid
     * @param mixed $token
     * @return array
     */
    public function find($uid = null, $token = null)
    {
        if ($this->saveable && ! $this->items && $uid && $token) {
            $this->items = $this->model()
                    ->select('*')
                    ->where(['user_id' => $uid, 'token' => &$token])
                    ->findOne();
        }
        if (! $this->items && $uid && $token && $this->useCache()) {
            $this->items = (array)$this->user->cache()->get(
                $this->normalizeKey($uid, $token)
            );
        }

        return $this->items;
    }

    /**
     * 查找token加密随机码
     * 如果失效则返回false
     *
     * @param $uid
     * @param $token
     * @return false|string
     */
    public function findEncryptCode($uid, $token)
    {
        $this->find($uid, $token);

        if ($this->isActive()) {
            return false;
        }

        return $this->item('key');
    }

    /**
     * 获取用户所有有效token
     *
     * @param $uid
     * @return array
     */
    public function findActiveTokens($uid)
    {
        $cache = $this->user->cache();

        // 检测缓存中是否存在登录日志信息
        if ($data = $cache->get($uid)) {
            $time = time();

            $inactives = [];
            $actives   = [];
            foreach ($data as $k => &$v) {
                if (! $v) {
                    unset($data[$k]);
                    continue;
                }
                // 检测token是否过期
                list($id, $token, $app, $expireAt) = $this->parseUserIdValue($v);
                if ($expireAt < $time) {
                    $inactives[] = $id;
                    unset($data[$k]);
                } else {
                    $actives[] = ['id' => $id, 'token' => $token, 'app' => $app, 'user_id' => $uid];
                }

            }
            // 移除过期token
            $cache->set($uid, $data);
            if ($this->saveable && $inactives) {
                $this->setInActiveForIds($inactives);
            }

            if ($actives) {
                return $actives;
            }
        }

        if (! $this->saveable) {
            return [];
        }

        // 缓存中没有数据，则从数据库中查找登录日志
        return $this->findActivesTokenInDatabase($uid);
    }

    /**
     * 从数据库中查找为过期的token
     *
     * @param $uid
     * @return array
     */
    protected function findActivesTokenInDatabase($uid)
    {
        if (!$data = $this->findActivesForUserId($uid)) {
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
                $actives[] = ['id' => $v['id'], 'token' => $v['token'], 'app' => $v['app'], 'user_id' => $uid];
            }
        }
        
        if ($inactives) {
            $this->setInActiveForIds($inactives);
        }

        return $actives;

    }

    protected function setInActiveForIds(array $ids)
    {
        if (count($ids) > 1) {
            $where = ['id' => ['IN', $ids]];
        } else {
            $where = ['id' => $ids[0]];
        }
        return $this->model()->where($where)->update(['active' => 0]);
    }

    protected function findActivesForUserId($uid)
    {
        $this->rows = $this->model()
            ->select('*')
            ->where(['user_id' => $uid, 'active' => 1])
            ->find();

        return $this->rows;
    }


    /**
     * 获取最新登录日志
     *
     * @return array
     */
    public function findActiveLatestLoginedLog($uid = null)
    {
        $uid = $uid ?: $this->user->model()->getId();

        if (! $uid) return [];

        return $this->model()
            ->select('life,token,id,created_at,device,ip')
            ->where(
                [
                    'user_id' => $uid,
                    'active'  => 1,
                    'app'     => $this->user->app(),
                    'type'    => $this->getType()
                ]
            )
            ->sort('id DESC')
            ->findOne();
    }

}
