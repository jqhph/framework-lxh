<?php

namespace Lxh\RequestAuth;

use Lxh\Cache\CacheInterface;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\MVC\Model;
use Lxh\RequestAuth\Cache\Cache;
use Lxh\RequestAuth\Cache\TokenCache;
use Lxh\RequestAuth\Database\FindLog;
use Lxh\RequestAuth\Database\User;
use Lxh\RequestAuth\Entities\Log as LogEntity;
use Lxh\RequestAuth\Entities\Log;
use Lxh\RequestAuth\Exceptions\UnsupportedEncryptionException;
use Lxh\RequestAuth\Exceptions\UserIdNotFoundException;

class Token
{
    use FindLog;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var TokenCache
     */
    protected $cache;

    /**
     * @var Model
     */
    protected $model;

    /**
     * token模型名称
     *
     * @var string
     */
    protected $modelName;

    /**
     * @var string
     */
    protected $encrypt;

    /**
     * 是否保存登录日志
     *
     * @var bool
     */
    protected $storable = false;

    public function __construct(Auth $auth, CacheInterface $cache)
    {
        $this->auth  = $auth;
        $this->user  = $auth->user();
        $this->cache = new TokenCache($cache);

        $this->modelName = $auth->option('log-model') ?: 'admin_login_log';
        $this->encrypt   = $auth->option('encrypt') ?: 'sha256';
        $this->storable  = $auth->option('storable');

    }

    /**
     * 获取token
     *
     * @return string
     */
    public function get()
    {
        return $this->token;
    }

    /**
     *
     *
     * @param Log $log
     * @return $this
     */
    public function setLog(Log $log)
    {
        $this->log   = $log;
        $this->token = $log->token;

        return $this;
    }

    /**
     * 检查token是否可用
     *
     * @param $token
     * @return bool
     */
    public function isTokenEffective($token)
    {
        return (bool) $this->cache->getForToken($token);
    }

    /**
     * 判断token是否有效
     *
     * @return bool
     */
    public function isActive()
    {
        if (
            $this->log->active == 0 || time() > ($this->log->created_at + $this->log->life)
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param User $user
     * @param bool $remember
     * @return LogEntity
     */
    public function createAndSave($remember = false)
    {
        $key    = $this->generateCode();
        $token  = $this->token = $this->generate($key);
        $life   = $remember ? $this->auth->getLifetime() : 0;
        $userId = $this->user->getId();

        $items = [
            'token'      => &$token,
            'ip'         => ip2long(request()->ip()),
            'created_at' => time(),
            'key'        => &$key,
            'active'     => 1,
            'user_id'    => $userId,
            'life'       => $life,
            'device'     => 0,
            'app'        => $this->auth->app(),
            'type'       => $this->getType(),
        ];

        $this->log = new LogEntity($items);

        // 保存到数据库
        if ($this->storable) {
            $this->insert();
        }
        // 保存到缓存
        if ($remember) {
            $this->cache->setForUserIdAndToken($userId, $token, $this->log->toArray(), $life);
            // 缓存加密key
            $this->cache->setEncryptCode(
                $this->auth->driver()->normalizeCookieValue($token),
                $this->log->key,
                $life
            );
        }
        $this->cache->appendForUserId($userId, $this->log);
        $this->cache->setActiveForToken($this->log->token, $this->auth->getLifetime());

        return $this->log;
    }

    /**
     * 保存登录日志到数据库
     *
     */
    protected function insert()
    {
        $model = $this->model();

        $model->attach($this->log->toArray());

        if (!$id = $model->add()) {
            logger('request-auth')->error('保存用户登录日志失败', $this->log->toArray());
        }
        // 保存id
        $this->log->id = $id;
    }

    /**
     * 类型
     * session站点登录，类型为1
     *
     * @return int
     */
    protected function getType()
    {
        return 1;
    }

    /**
     * 生成token
     *
     * @param string $code 加密随机码
     * @return bool|string
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function generate($code)
    {
        $target = $this->auth->driver()->getEncryptTarget();

        if (empty($code) || empty($target)) {
            throw new InvalidArgumentException('目标加密字符串和加密随机码不能为空！');
        }

        switch ($this->encrypt) {
            case 'password_hash':
                return
                    password_hash($target, PASSWORD_DEFAULT, ['salt' => &$code]);

                break;
            case 'sha256':
                return hash('sha256', $target.$code);

                break;
            default:
                throw new UnsupportedEncryptionException('不支持的加密方式：' . $this->encrypt);
        }
    }

    /**
     * 验证token是否正确
     *
     * @param string $token
     * @param string $code 随机字符串
     * @return bool
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function check($token, $code)
    {
        $target = $this->auth->driver()->getEncryptTarget();

        if (empty($token) || empty($target)) {
            throw new InvalidArgumentException('目标加密字符串和token不能为空！');
        }

        switch ($this->encrypt) {
            case 'password_hash':
                return password_verify(password_hash($target, PASSWORD_DEFAULT, ['salt' => &$code]), $token);

                break;
            case 'sha256':
                return hash('sha256', $target.$code) == $token;

                break;
            default:
                throw new UnsupportedEncryptionException('不支持的加密方式：' . $this->encrypt);
        }

    }

    /**
     * 生成加密随机码
     *
     * @return string
     */
    public function generateCode()
    {
        return Util::randomString(6).uniqid(microtime(true));
    }

    /**
     * 用户主动注销token
     * 把token状态设置为无效
     *
     * @param $logId
     * @param $token
     * @return mixed
     */
    public function setInactiveByUser($token, $logId = 0)
    {
        $userId = $this->user->getId();

        if (! $userId) {
            throw new UserIdNotFoundException;
        }

        if ($this->storable) {
            if (! $this->updateInactiveByUser($token, $logId)) {
                logger('request-auth')->error(
                    '用户登出，设置token无效时出错！', ['uid' => $userId, 'logid' => $logId, 'token' => &$token]
                );
            }
        }

        $this->deleteCache($userId, $token);
    }

    /**
     * 把token状态设置为无效
     *
     * @param $token
     * @param $logId
     * @return mixed
     */
    public function setInactive($token, $logId = 0)
    {
        $userId = $this->user->getId();

        if (! $userId) {
            throw new UserIdNotFoundException;
        }

        if ($this->storable) {
            if ($logId) {
                if (! $this->updateInactiveForIds([$logId])) {
                    logger('request-auth')->error(
                        '设置token无效时出错！', ['uid' => $userId, 'logid' => $logId, 'token' => &$token]
                    );
                }
            } else {
                if (! $this->updateInactiveForToken($token)) {
                    logger('request-auth')->error(
                        '设置token无效时出错！', ['uid' => $userId, 'logid' => $logId, 'token' => &$token]
                    );
                }
            }

        }

        $this->deleteCache($userId, $token);
    }

    /**
     * 移除缓存数据
     *
     * @param $userId
     * @param $token
     */
    protected function deleteCache($userId, $token)
    {
        $this->cache->deleteInArrayKeyByUserIdWhenEqToken($userId, $token);
        $this->cache->deleteForToken($token);
        $this->cache->deleteForUserIdAndToken($userId, $token);
        $this->cache->deleteEncryptCode(
            $this->auth->driver()->normalizeCookieValue($token)
        );
    }

    /**
     * 用户主动注销
     *
     * @param $id
     * @return $this
     */
    protected function updateInactiveByUser($token, $id = 0)
    {
        if ($id) {
            $where = ['id' => $id];
        } else {
            $where = ['token' => $token];
        }

        return $this->model()
            ->where($where)
            ->update(['active' => 0, 'logout_at' => time()]);
    }

    /**
     * 查找出用户所有有效的token
     *
     * @return array
     */
    public function findActiveTokens()
    {
        $userId = $this->user->getId();

        if (! $userId) {
            throw new UserIdNotFoundException;
        }

        // 检测缓存中是否存在登录日志信息
        if ($data = $this->cache->getArrayForUserId($userId)) {
            $time = time();

            $inactives = [];
            $actives   = [];
            foreach ($data as $k => &$v) {
                if (! $v) {
                    unset($data[$k]);
                    continue;
                }
                // 检测token是否过期
                list($id, $token, $app, $expireAt) = $this->cache->parseUserIdValue($v);
                if ($expireAt < $time) {
                    $inactives[] = $id;
                    unset($data[$k]);
                } else {
                    $actives[] = [
                        'id' => $id, 'token' => $token, 'app' => $app, 'user_id' => $userId
                    ];
                }

            }
            // 移除失效token后重新保存
            $this->cache->setArrayForUserId($userId, $data);
            if ($this->storable && $inactives) {
                $this->updateInactiveForIds($inactives);
            }

            if ($actives) {
                return $actives;
            }
        }

        if (! $this->storable) {
            return [];
        }

        // 缓存中没有数据，则从数据库中查找登录日志
        return $this->findActivesTokenInDatabase($userId);
    }

    /**
     * 被动注销token
     * 根据日志id设置token日志为无效
     *
     * @param array $ids
     * @return $this
     */
    protected function updateInactiveForIds(array $ids)
    {
        if (count($ids) > 1) {
            $where = ['id' => ['IN', &$ids]];
        } else {
            $where = ['id' => &$ids[0]];
        }
        return $this->model()->where($where)->update(['active' => 0]);
    }

    protected function updateInactiveForToken($token)
    {
        return $this->model()->where('token', $token)->update(['active' => 0]);
    }

    /**
     * @return Model
     */
    public function model()
    {
        return $this->model ?: ($this->model = model($this->modelName));
    }

}
