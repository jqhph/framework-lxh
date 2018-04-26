<?php

namespace Lxh\RequestAuth;

use Lxh\Cache\CacheInterface;
use Lxh\RequestAuth\Cache\Cache;
use Lxh\RequestAuth\Cache\File;
use Lxh\RequestAuth\Database\Log;
use Lxh\RequestAuth\Database\User;
use Lxh\RequestAuth\Drivers\Driver;
use Lxh\RequestAuth\Drivers\Session;
use Lxh\RequestAuth\Exceptions\AuthTokenException;
use Lxh\RequestAuth\Exceptions\UserNotExistException;
use Lxh\RequestAuth\Entities\Log as LogEntity;

class Auth
{
    /**
     * 配置选项
     *
     * @var array
     */
    protected $options = [
        // 是否不限制用户使用多个客户端登录
        'allowed-multiple-logins' => false,
        // 记住登录状态时间，默认7天
        'remember-life'           => 604800,
        // 是否可存储
        'storable'                => true,
        // 登陆日志模型名称
        'log-model'               => 'user_login_log',
        // 鉴权认证驱动
        // 不填会根据isOpen参数判断使用哪个驱动
        'driver'                  => Session::class,
        // 缓存驱动
        'cache-channel'           => 'request-auth',
        // password_hash, sha256
        'encrypt'                 => 'sha256',
        // 应用类型，必须是一个0-99的整数
        // 对于站内session模式登录，此参数用于保证用户在同一类型下的应用只能保留一个有效的登陆状态
        // 对于开放授权token模式登录，此参数用于保证用户在同一类型下的应用只能获取一个有效授权token
        'app'                     => 0,
        // 连续登陆错误长间隔时间（秒）
        'reject-interval'         => 600,
        // 用于区分前后台用户
        'user-type'               => 1
    ];

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var Counters\Counter
     */
    protected $counter;

    public function __construct(User $user, array $options = [])
    {
        $this->setUser($user);

        $this->options = array_merge($this->options, $options);
        $this->cache   = cache_factory()->get(getvalue($this->options, 'cache-channel', 'request-auth'));
        $this->token   = new Token($this, $this->cache);

        $driver = $this->options['driver'] ?: Session::class;
        $this->setDriver(new $driver($this));
    }

    /**
     * 设置或获取配置选项
     *
     * @param string $k
     * @param null|mixed $v
     * @return $this|mixed|null
     */
    public function option($k, $v = null)
    {
        if ($v === null) {
            return isset($this->options[$k]) ? $this->options[$k] : null;
        }

        $this->options[$k] = &$v;
        return $this;
    }

    /**
     * @return Token
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * 检查用户是否已登录
     *
     * @return bool
     */
    public function check()
    {
        return $this->driver->check();
    }

    /**
     * 用户登录方法
     *
     * @param string $username
     * @param string $password
     * @return array|false
     * @param array $options 可拓展参数
     * @return bool
     *
     * @throws AuthTokenException
     * @throws UserNotExistException
     */
    public function login($username, $password, $remember = false, array $options = [])
    {
        $this->username = &$username;

        try {
            $data = $this->user->login($username, $password, $options);
        } catch (\Exception $e) {
            $this->counter()->incr($username);

            throw new $e;
        }

        $this->attachToUser($data);

        // 判断是否应该先把旧token设置为无效
        if ($log = $this->findActiveTokensForSameApp()) {
            $this->inactive();
        }

        // 生成登录日志并保存
        $log = $this->token->createAndSave($remember);

        $this->setUserLog($log);

        // 缓存用户登录信息
        $this->driver->logged($remember);

        return true;
    }

    /**
     * 注入用户id到用户模型
     *
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user->setId($userId);

        return $this;
    }

    /**
     * 注入用户信息到用户模型
     *
     * @param array $items
     * @return $this
     */
    public function attachToUser(array $items)
    {
        $this->user->attach($items);

        return $this;
    }

    public function setUserLog(LogEntity $log)
    {
        $this->user->setLog($log);

        return $this;
    }

    /**
     * 设置token无效（非用户主动登出）
     *
     * @param $userId
     * @param $token
     * @param $logId
     * @return mixed
     */
    public function inactive($token = null, $logId = 0)
    {
        return $this->driver->setInactive($token, $logId);
    }


    /**
     * 用户主动登出
     * 把token设置为无效
     *
     * @return mixed
     */
    public function logout()
    {
        return $this->driver->logout();
    }

    /**
     * 获取应用类型
     *
     * @return int
     */
    public function app()
    {
        return $this->options['app'];
    }

    /**
     * 获取token生命周期
     *
     * @param bool $remember
     * @return Auth|mixed|null
     */
    public function getLifetime()
    {
        return $this->option('remember-life');
    }

    /**
     * @param User $user
     * @return LogEntity|false
     */
    protected function findActiveTokensForSameApp()
    {
        if (
            $this->options['allowed-multiple-logins']
        ) {
            return false;
        }

        // 保证用户在同一类型下的应用只能获取一个有效授权token
        if (! $logs = $this->token->findActiveTokens()) {
            return false;
        }

        // 获取用户登录入口应用类型
        $app = $this->app();
        foreach ($logs as &$v) {
            if ($app == $v['app']) {
                // 已存在相同入口
                // 需要把该用户踢下线
                return new LogEntity($v);
            }
        }
        return false;
    }

    /**
     * 获取用户登录失败次数
     *
     * @param string $username
     * @return int
     */
    public function getRejectTimes($username = null)
    {
        return $this->counter()->total($username ?: $this->username);
    }

    /**
     * 重置用户登录失败次数
     *
     * @param string $username
     * @return mixed
     */
    public function resetRejectTimes($username = null)
    {
        return $this->counter()->reset($username ?: $this->username);
    }


    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return Cache
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * @return Driver
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * 设置缓存对象
     *
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * 设置用户模型
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function counter()
    {
        return $this->counter ?: ($this->counter = new Counters\Counter($this));
    }

}
