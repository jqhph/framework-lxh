<?php

namespace Lxh\OAuth\Database;

use Lxh\MVC\Model;
use Lxh\OAuth;

abstract class User extends Model
{
    /**
     * @var OAuth\User
     */
    protected $oauth;

    /**
     * oauth配置键名
     *
     * @var string
     */
    protected $oauthKeyName = 'admin';

    /**
     * 判断是否是_ife发过来的请求
     *
     * @var string
     */
    protected $iframeKeyName = '_ife';

    /**
     * 用户登录方法
     * 成功必须返回一个数组
     *
     * @param string $username
     * @param string $password
     * @return array|false
     * @param array $options
     * @return false|array
     */
    abstract public function login($username, $password, array $options = []);

    /**
     * 获取需要保存到登录缓存中的用户数据
     *
     * @return array|false
     */
    abstract public function findForLogined();

    /**
     *
     * @return OAuth\User
     */
    public function oauth()
    {
        return $this->oauth ?:
            ($this->oauth = new OAuth\User(
                $this, (array)config('oauth.'.$this->oauthKeyName)
            ));
    }

    /**
     * 设置登录日志
     * 
     * @param array $logs
     * @return $this
     */
    public function setLogs(array $logs)
    {
        return $this->set('logs', $logs);
    }

    /**
     * 获取登陆日志数据
     *
     * @param null $key
     * @return mixed
     */
    public function logs($key = null)
    {
        return $this->get($key ? 'logs.'.$key : 'logs');
    }
}
