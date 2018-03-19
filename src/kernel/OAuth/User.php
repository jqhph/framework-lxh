<?php

namespace Lxh\OAuth;

use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\OAuth\Cache\Cache;
use Lxh\OAuth\Cache\File;
use Lxh\OAuth\Database\Logs;
use Lxh\OAuth\Database;
use Lxh\Events\Dispatcher;
use Lxh\MVC\Model;
use Lxh\OAuth\Drivers\Driver;
use Lxh\OAuth\Drivers\Session;
use Lxh\OAuth\Drivers\Token as TokenDriver;
use Lxh\OAuth\Drivers\Session as SessionDriver;
use Lxh\OAuth\Exceptions\UnsupportedEncryptionException;

/**
 * 用户身份鉴权类
 *
 * @author Jqh
 * @date   2018/3/15 14:15
 */
class User
{
    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * 用户模型
     *
     * @var Database\User
     */
    protected $model;

    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @var Logs
     */
    protected $logs;

    /**
     * 配置选项
     *
     * @var array
     */
    protected $options = [
        // 是否不限制用户使用多个客户端登录
        'allowed-multiple-logins' => false,
        // 登录有效期，如果使用session登录，则此参数无效
        'life'                    => 7200,
        // 记住登录状态时间，默认7天
        'long-life'               => 604800,
        // 启用登录日志
        'use-log'                 => true,
        // 登陆日志模型名称
        'log-model'               => 'user_login_log',
        // 鉴权认证驱动
        // 不填会根据isOpen参数判断使用哪个驱动
        'driver'                  => '',
        // 是否使用token验证，默认false
        // 当值为true时，使用token验证用户是否登录
        // 当值为false时，启用session存储用户登录信息
        'isOpen'                  => false,
        // 日志处理类
        'log-handler'             => Logs::class,
        // 缓存驱动
        'cache-driver'            => File::class,
        // token参数名称
        'tokenKey'                => 'access_token',
        // password_hash, sha256
        'encrypt'                 => 'sha256',
        // 生成token加密密钥
        'secretKey'               => '',
        // 应用类型，必须是一个0-99的整数
        // 对于站内session模式登录，此参数用于保证用户在同一类型下的应用只能保留一个有效的登陆状态
        // 对于开放授权token模式登录，此参数用于保证用户在同一类型下的应用只能获取一个有效授权token
        'app'                     => 0,
    ];

    /**
     * 缓存对象
     * 默认使用session保存
     *
     * @var Cache
     */
    protected $cache;

    /**
     * 计数器
     *
     * @var mixed
     */
    protected $counter;

    public function __construct(Database\User $user, array $options = [])
    {
        $this->events  = events();
        $this->options = array_merge($this->options, $options);

        $cache = $this->options['cache-driver'] ?: File::class;
        $this->setCache(new $cache());

        $logs = $this->options['log-handler'] ?: Logs::class;
        $this->setLogs(new $logs($this));

        $this->setModel($user);

        $driver = $this->options['driver'] ?: ($this->options['isOpen'] ? TokenDriver::class : SessionDriver::class);
        $this->setDriver(new $driver($this));

        $this->options['app'] = (int)$this->options['app'];
        if ($this->options['app'] > 99 || $this->options['app'] < 0) {
            throw new InvalidArgumentException(
                '应用类型参数必须是一个0-99的整数！'
            );
        }
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
     * 用户登录方法
     *
     * @param string $username
     * @param string $password
     * @return array|false
     * @param array $options 可拓展参数
     * @return bool
     */
    public function login($username, $password, $remember = false, array $options = [])
    {
        if (!$data = $this->model->login($username, $password, $options)) {
            return false;
        }
        $this->model->attach($data);

        // 是否应该先把旧token设置为无效
        if ($log = $this->setInactiveAble($this->model)) {
            $this->inactive($log['user_id'], $log['id'], $log['token']);
        }

        // 生成登录日志
        $logs = $this->logs->create($this->model, $remember);

        $this->model->setLogs($logs);

        // 缓存用户登录信息
        $this->driver->save($this->model, $remember);

        return true;
    }

    /**
     * 获取token
     *
     * @return string
     */
    public function token()
    {
        return $this->model->logs('token');
    }

    /**
     * 判断token是否有效
     *
     * @return bool
     */
    public function isTokenActive()
    {

    }

    /**
     * 把token设置为无效
     *
     * @param $userId
     * @param $logId
     * @param $token
     */
    public function inactive($userId, $logId, $token)
    {
        $this->driver->inactive($userId, $logId, $token);
    }

    /**
     * 是否应该先设置旧token为无效
     *
     * @param Database\User $user
     * @return bool
     */
    protected function setInactiveAble(Database\User $user)
    {
        if (
            $this->options['allowed-multiple-logins'] && ! $this->isOpen()
        ) {
            return false;
        }

        // 保证用户在同一类型下的应用只能获取一个有效授权token
        if (! $logs = $this->logs->findActiveTokens($user->getId())) {
            return false;
        }

        // 获取用户登录入口应用类型
        $app = $this->app();
        foreach ($logs as &$v) {
            if ($app == $v['app']) {
                // 已存在相同入口
                // 需要把该用户踢下线
                return $v;
            }
        }
        return false;

    }

    /**
     * 判断是否是开放token认证
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->options['isOpen'];
    }

    /**
     * 获取token登录有效期
     *
     * @param bool $remember
     * @return int
     */
    public function getLife($remember = false)
    {
        if ($remember) {
            return $this->option('long-life') ?: 604800;
        }
        return $this->option('life') ?: 7200;
    }

    /**
     * 生成token
     *
     * @param Database\User $target 目标加密字符串
     * @param string $code 加密随机码
     * @return bool|string
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function generateToken(Database\User $target, $code)
    {
        $target = $this->driver->getEncryptTarget($target);

        if (empty($code) || empty($target)) {
            throw new InvalidArgumentException('目标加密字符串和加密随机码不能为空！');
        }

        switch ($this->options['encrypt'] ?: 'sha256') {
            case 'password_hash':
                return
                    password_hash($target, PASSWORD_DEFAULT, ['salt' => $code]);

                break;
            case 'sha256':
                return hash('sha256', $target.$code);
                
                break;
            default:
                throw new UnsupportedEncryptionException('不支持的加密方式：' . $this->options['encrypt']);
        }
    }

    /**
     * 验证token是否正确
     *
     * @param string $token
     * @param string $code 随机字符串
     * @param Database\User $target 
     * @return bool
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function vertifyToken($token, Database\User $target, $code)
    {
        $target = $this->driver->getEncryptTarget($target);

        if (empty($token) || empty($target)) {
            throw new InvalidArgumentException('目标加密字符串和token不能为空！');
        }

        switch ($this->options['encrypt'] ?: 'sha256') {
            case 'password_hash':
                return password_verify($target, $token);

                break;
            case 'sha256':
                return hash('sha256', $target.$code) == $token;

                break;
            default:
                throw new UnsupportedEncryptionException('不支持的加密方式：' . $this->options['encrypt']);
        }

    }

    /**
     * 获取用户登录失败次数
     *
     * @return int
     */
    public function defeated()
    {
        return $this->counter->total();
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
     * 用户登出方法
     *
     * @return bool
     */
    public function logout()
    {
        return $this->driver->logout();
    }

    /**
     * 设置密钥
     *
     * @param $code
     * @return $this
     */
    public function setSecretCode($code)
    {
        $this->options['secretKey'] = $code;
        return $this;
    }

    /**
     * 获取密钥
     *
     * @return mixed
     */
    public function getSecretCode()
    {
        return $this->options['secretKey'];
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
     * @return Logs
     */
    public function logs()
    {
        return $this->logs;
    }

    /**
     * 
     * @param Database\LogsInterface $logs
     * @return $this
     */
    public function setLogs(Database\LogsInterface $logs)
    {
        $this->logs = $logs;
        return $this;
    }

    /**
     *
     * @return Cache
     */
    public function cache()
    {
        return $this->cache;
    }

    public function setCache(Cache $cache)
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
     * Get or set option
     *
     * @param string $key
     * @param mixed  $value
     * @return $this|mixed
     */
    public function option($key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key];
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @return Database\User
     */
    public function model()
    {
        return $this->model;
    }

    public function setModel(Database\User $model)
    {
        $this->model = $model;

        return $this;
    }

    public function counter()
    {

    }

}
