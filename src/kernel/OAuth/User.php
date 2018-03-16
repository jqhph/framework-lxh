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
        // 是否允许多端登录
        'allowed-multiple-logins' => false,
        // 登录有效期，如果使用session登录，则此参数无效
        'life'                    => 7200,
        // 记住登录状态时间，默认7天
        'long-life'               => 604800,
        // 启用登录日志
        'use-log'                 => true,
        // 登陆日志模型名称
        'log-model'               => 'user_login_log',
        // 认证类型
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
    ];

    /**
     * 缓存对象
     * 默认使用session保存
     *
     * @var Cache
     */
    protected $cache;

    public function __construct(array $options = [])
    {
        $this->events  = events();
        $this->options = array_merge($this->options, $options);

        if ($this->options['isOpen']) {
            $this->driver = new TokenDriver($this);
        } else {
            $this->driver = new SessionDriver($this);
        }

        $cache = $this->options['cache-driver'] ?: File::class;
        $this->setCache(new $cache());

        $logs = $this->options['log-handler'] ?: Logs::class;
        $this->setLogs(new $logs($this));

        $this->setModel(user());
    }

    /**
     * 用户登录方法
     *
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function login($username, $password, $remember = false)
    {
        if (!$data = $this->model->login($username, $password)) {
            return false;
        }

        // 生成登录日志
        $data['logs'] = $this->logs->create($this->model, $remember);

        $this->model->attach($data);

        // 保存用户登录信息
        $this->driver->save($this->model, $remember);

        return true;
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
     * @param Model $target 目标加密字符串
     * @param string $code 加密随机码
     * @return bool|string
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function generateToken($target, $code)
    {
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
     * @param Model $target 目标加密字符串
     * @return bool
     * @throws InvalidArgumentException
     * @throws UnsupportedEncryptionException
     */
    public function vertifyToken($token, $target, $code)
    {
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

}