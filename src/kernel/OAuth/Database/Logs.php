<?php

namespace Lxh\OAuth\Database;

use Lxh\OAuth\User as OAuth;
use Lxh\MVC\Model;

/**
 *
 * @author Jqh
 * @date   2018/3/15 14:15
 */
class Logs implements LogsInterface
{
    use CacheLogs, FindLogs;

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
        // 应用类型，必须是一个0-99的整数
        // 对于站内session模式登录，此参数用于保证用户在同一类型下的应用只能保留一个有效的登陆状态
        // 对于开放授权token模式登录，此参数用于保证用户在同一类型下的应用只能获取一个有效授权token
        'app',
        // 1站内session登录，2授权开放登录
        'type',
    ];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var OAuth
     */
    protected $user;

    /**
     * @var Model
     */
    protected $model;

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

    /**
     * 判断是否是开放授权登录模式
     *
     * @var bool
     */
    protected $isOpen = false;

    public function __construct(OAuth $user, array $items = [])
    {
        $this->user      = $user;
        $this->items     = &$items;
        $this->modelName = $user->option('log-model') ?: 'UserLoginLog';
        $this->saveable  = $user->option('use-log');
        $this->isOpen    = $user->isOpen();
        $this->cache     = $user->cache();
    }

    /**
     * 判断token是否有效
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->item('active') == 0 || time() > ($this->item('created_at') + $this->item('life'))) {
            return false;
        }
        return true;
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

    public function setItems(array $items)
    {
        $this->items = &$items;

        return $this;
    }

    /**
     * 登出时把日志状态设置为无效
     *
     * @return void
     */
    public function logout()
    {
        if ($this->saveable) {
            $user = $this->user->model();
            if ($id = $user->logs('id')) {
                if (! $this->updateInactive($id)) {
                    logger('oauth')->error(
                        '登出时修改登陆日志状态出错！', $user->toArray()
                    );
                }
            }
        }

        $this->deleteItemsInCache();
        $this->deleteForUserId();
        $this->deleteForToken();        
    }

    /**
     * 把token状态设置为无效
     *
     * @param $userId
     * @param $logId
     * @param $token
     * @return mixed
     */
    public function inactive($userId, $logId, $token)
    {
        if ($this->saveable) {
            if (! $this->updateInactive($logId)) {
                logger('oauth')->error(
                    '设置登录token无效时出错！', ['uid' => $userId, 'logid' => $logId, 'token' => $token]
                );
            }
        }

        $this->deleteForUserId($userId, $token);
        $this->deleteItemsInCache($userId, $token);
        $this->deleteForToken($token);
    }

    protected function updateInactive($id)
    {
        return $this->model()
            ->where('id', $id)
            ->update(['active' => 0, 'logout_at' => time()]);
    }

    /**
     * 生成日志记录
     *
     * @param \Lxh\OAuth\Database\User $user
     * @param bool $remember
     */
    public function create(\Lxh\OAuth\Database\User $user, $remember = false)
    {
        $key   = $this->user->generateCode();
        $token = $this->user->generateToken($user, $key);
        $uid   = $user->getId();
        $life  = $this->user->getLife($remember);

        $this->items = [
            'token'      => &$token,
            'ip'         => request()->ip(),
            'created_at' => time(),
            'key'        => &$key,
            'active'     => 1,
            'user_id'    => $uid,
            'life'       => $life,
            'device'     => 0,
            'app'      => $this->user->option('app'),
            'type'       => $this->isOpen ? 2 : 1,
        ];

        if ($this->saveable) {
            $this->save();
        }

        $this->saveCache($uid);

        return $this->items;

    }

    protected function save()
    {
        $model = $this->model();

        $model->attach($this->items);

        if (!$id = $model->add()) {
            logger()->error('保存登录日志失败', $this->items);
        }
        // 保存id
        $this->items['id'] = $id;
    }

    /**
     * @return Model
     */
    public function model()
    {
        return $this->model ?: ($this->model = model($this->modelName));
    }

    protected function normalizeKey($uid, $token)
    {
        return $uid.'_'.$token;
    }

    public static function columns()
    {
        return static::$columns;
    }
}