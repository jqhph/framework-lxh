<?php

namespace Lxh\RequestAuth\Database;

use Lxh\MVC\Model;
use Lxh\RequestAuth;

abstract class User extends Model
{
    /**
     * @var RequestAuth\Auth
     */
    protected $auth;

    /**
     * oauth配置键名
     *
     * @var string
     */
    protected $authKeyName = 'admin';

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
    abstract public function findForLogged();

    /**
     * 获取加密类型
     *
     * @return int
     */
    abstract public function getEncryptType();

    /**
     *
     * @return RequestAuth\Auth
     */
    public function auth()
    {
        return $this->auth ?:
            ($this->auth = new RequestAuth\Auth(
                $this, (array)config('request-auth.'.$this->authKeyName)
            ));
    }

    /**
     * 设置登录日志
     * 
     * @param RequestAuth\Entities\Log $log
     * @return $this
     */
    public function setLog($log)
    {
        return $this->set('_log', $log);
    }

    /**
     * 获取登陆日志数据
     *
     * @param null $key
     * @return RequestAuth\Entities\Log
     */
    public function log()
    {
        return $this->get('_log');
    }

    public function attach(array $data)
    {
        if (!empty($data['_log'])) {
            $data['_log'] = new RequestAuth\Entities\Log($data['_log']);
        }

        return parent::attach($data); // TODO: Change the autogenerated stub
    }

    public function toArray()
    {
        $items = parent::toArray();

        $items['_log'] = !empty($items['_log']) ? $items['_log']->toArray() : [];

        return $items;
    }
}
