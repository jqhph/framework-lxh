<?php

namespace Lxh\OAuth\Database;

use Lxh\OAuth\User;
use Lxh\MVC\Model;

/**
 *
 * @author Jqh
 * @date   2018/3/15 14:15
 */
class Logs implements LogsInterface
{
    /**
     * 登录日志字段
     *
     * @var array
     */
    protected static $columns = [
        'id',
        // token
        'token',
        // 加密随机字符串
        'key',
        // 登录时间
        'created_at',
        // 登出时间
        'logout_at',
        // 登录ip
        'ip',
        // 登录设备（预留字段）
        'device',
        // 是否有效 1 0
        'active',
        // token有效期（秒）
        'life',
        // 登录用户id
        'user_id',
    ];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var User
     */
    protected $user;

    /**
     * 日志模型名称
     *
     * @var string
     */
    protected $modelName;

    /**
     * 是否保存日志到数据表
     *
     * @var bool
     */
    protected $saveable = true;

    public function __construct(User $user)
    {
        $this->user      = $user;
        $this->modelName = $user->option('log-model') ?: 'UserLoginLog';
        $this->saveable  = $user->option('use-log');
    }

    /**
     * 获取登陆日志数据
     *
     * @param mixed $id
     * @param mixed $token
     * @return array
     */
    public function find($id = null, $token = null)
    {
        if ($this->saveable && ! $this->items && $id && $token) {
            $this->items = (array)model($this->modelName)
                ->select('*')
                ->where(['user_id' => $id, 'token' => $token])
                ->findOne();
        }
        if (! $this->items && $id && $token && $this->useCache()) {
            $this->items = (array)$this->user->cache()->get(
                $this->normalizeKey($id, $token)
            );
        }

        return $this->items;
    }

    /**
     * 查找token加密随机码
     *
     * @param $id
     * @param $token
     * @return array|mixed
     */
    public function findEncryptCode($id, $token)
    {
        $this->find($id, $token);

        return $this->item('key');
    }

    /**
     *
     * @param mixed $key
     * @return array|mixed
     */
    public function item($key = null)
    {
        if (!$key) return $this->items;

        return get_value($this->items, $key);
    }

    /**
     * 登出时把日志状态设置为无效
     *
     * @return void
     */
    public function inactive()
    {
        $user = $this->user->model();

        if ($this->saveable) {
            if ($id = $user->get('logs.id')) {
                if (!model($this->modelName)->where('id', $id)->update(['active' => 0, 'logout_at' => time()])) {
                    logger('oauth')->error(
                        '登出时修改登陆日志状态出错！', $user->toArray()
                    );
                }
            }
        }

        if ($this->useCache()) {
            $this->user->cache()->delete(
                $this->normalizeKey($user->getId(), $user->get('logs.token'))
            );
        }
    }

    /**
     * 生成日志记录
     *
     * @param \Lxh\OAuth\Database\User $user
     * @param bool $remember
     */
    public function create(\Lxh\OAuth\Database\User $user, $remember = false)
    {
        $key = $token = '';
        if ($this->user->isOpen()) {
            $key   = $this->user->generateCode();
            $token = $this->user->generateToken($user, $key);
        }

        $id   = $user->getId();
        $life = $this->user->getLife($remember);

        $this->items = [
            'token'      => &$token,
            'ip'         => request()->ip(),
            'created_at' => time(),
            'key'        => &$key,
            'active'     => 1,
            'user_id'    => $id,
            'life'       => $life,
            'device'     => 0,
        ];

        if ($this->saveable) {
            $model = model($this->modelName);

            $model->attach($this->items);

            if (!$id = $model->add()) {
                logger()->error('保存登录日志失败', $this->items);
            }
            // 保存id
            $this->items['id'] = $id;
        }

        if ($token && $this->useCache()) {
            // 缓存登录日志
            $this->user->cache()->set(
                $this->normalizeKey($id, $token), $this->items, $life + 5
            );
        }

        return $this->items;

    }

    /**
     * 判断是否可以缓存登录日志
     *
     * @return bool
     */
    protected function useCache()
    {
        return $this->user->isOpen() || !$this->saveable;
    }

    protected function normalizeKey($id, $token)
    {
        return $id.'_'.$token;
    }

    public static function columns()
    {
        return static::$columns;
    }
}