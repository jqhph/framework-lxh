<?php

namespace Lxh\MVC;

use Lxh\Auth\Database\Admin;
use Lxh\MVC\Model;
use Lxh\Cookie\Store AS Cookie;
use Lxh\Session\Store AS SessionStore;
use Lxh\Support\Password;

class Session extends Model
{
    /**
     * 缓存用户信息的session和cookie键名
     *
     * @var string
     */
    protected $sessionKey = '$admin';

    /**
     * @var SessionStore
     */
    protected $session;

    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * @var string
     */
    protected $passwordKey = 'password';

    /**
     * @var int
     */
    protected $defaultLoginTime = 2592000;

    protected function initialize()
    {
        $this->session = $this->container['session'];
        $this->cookie = $this->container['cookie'];

        $this->sessionKey = config('admin.session-key', $this->sessionKey);
    }

    /**
     * 缓存用户id到cookie
     *
     * @return void
     */
    public function saveCookie()
    {
        // 默认一个月免登陆
        setcookie($this->sessionKey, $this->getId(), time() + config('login-time', $this->defaultLoginTime));
    }


    /**
     * 缓存用户数据到session
     *
     * @return void
     */
    public function saveSession()
    {
        $data = [];
        foreach ($this->toArray() as $k => &$v) {
            if ($k == $this->passwordKey) continue;

            $data[$k] = $v;
        }

        $this->session->set($this->sessionKey, $data);

        $this->session->save();
    }

    /**
     * 注入session数据
     *
     * @return $this
     */
    public function setupSession()
    {
        // 检查session是否存在用户数据
        if ($this->session->has($this->sessionKey)) {
            $this->attach($this->session->get($this->sessionKey));
            return $this;
        }

        // 检测cookie是否存在用户数据
        if (! $this->cookie->has($this->sessionKey)) {
            return $this;
        }

        $this->set($this->primaryKeyName, $this->cookie->get($this->sessionKey));

        // 查出用户数据
        $this->find();

        // 保存到session
        $this->saveSession();

        return $this;
    }


}
