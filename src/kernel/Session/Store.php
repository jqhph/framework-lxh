<?php

namespace Lxh\Session;

use Lxh\Helper\Entity;
use Lxh\Helper\Util;

class Store extends Entity
{
    protected $init   = null;

    protected $config = [];

    public function __construct()
    {
        $this->config = (array) config('session');

        $this->init();

        $this->items = $_SESSION;
    }

    public function boot()
    {
        if (is_null($this->init)) {
            $this->init();
        } elseif (false === $this->init) {
            $this->start();
        }

        return $this;
    }

    public function start()
    {
        if (PHP_SESSION_ACTIVE != session_status()) {
            session_start();

            $this->init = true;
        }
    }

    public function setUseTransSid($val)
    {
        $this->config['use-trans-sid'] = $val;
        return $this;
    }

    public function setName($val)
    {
        $this->config['name'] = $val;
        return $this;
    }

    public function setPath($val)
    {
        $this->config['path'] = $val;
        return $this;
    }

    public function setDomain($val)
    {
        $this->config['domain'] = $val;
        return $this;
    }

    public function setExpire($val)
    {
        $this->config['expire'] = $val;
        return $this;
    }

    public function setSecure($val)
    {
        $this->config['secure'] = $val;
        return $this;
    }

    public function setHttponly($val)
    {
        $this->config['httponly'] = $val;
        return $this;
    }

    public function setUseCookies($val)
    {
        $this->config['use-cookies'] = $val;
        return $this;
    }

    public function setCacheLimiter($val)
    {
        $this->config['cache-limiter'] = $val;
        return $this;
    }

    public function setDriver($val)
    {
        $this->config['driver'] = $val;
        return $this;
    }

    public function setCacheExpire($val)
    {
        $this->config['cache-expire'] = $val;
        return $this;
    }

    public function config()
    {
        return $this->config;
    }

    /**
     * session初始化
     * @param array $this->config
     * @return void
     * @throws \Exception
     */
    protected function init()
    {
        $isDoStart = false;

        // 启动session
        if (PHP_SESSION_ACTIVE != session_status()) {
            // get_value($this->config, 'auto-start') !== false &&
            ini_set('session.auto_start', 0);
            $isDoStart = true;
        }

        !empty($this->config['id']) && session_id($this->config['id']);

        isset($this->config['use-trans-sid']) && ini_set('session.use_trans_sid', $this->config['use-trans-sid'] ? 1 : 0);

        isset($this->config['name']) && session_name($this->config['name']);

        isset($this->config['path']) && session_save_path($this->config['path']);

        isset($this->config['domain']) && ini_set('session.cookie_domain', $this->config['domain']);

        isset($this->config['secure']) && ini_set('session.cookie_secure', $this->config['secure']);

        isset($this->config['httponly']) && ini_set('session.cookie_httponly', $this->config['httponly']);

        isset($this->config['use-cookies']) && ini_set('session.use_cookies', $this->config['use_cookies'] ? 1 : 0);

        isset($this->config['cache-limiter']) && session_cache_limiter($this->config['cache_limiter']);

        isset($this->config['cache-expire']) && session_cache_expire($this->config['cache_expire']);

        if (isset($this->config['expire'])) {
            ini_set('session.gc_maxlifetime', $this->config['expire']);
            ini_set('session.cookie_lifetime', $this->config['expire']);
        }

        if (!empty($this->config['driver'])) {
            $this->driver();
        }

        if ($isDoStart) {
            $this->start();
        }
    }

    protected function driver()
    {
        // 读取session驱动
        $class = false !== strpos($this->config['driver'], '\\') ?
            $this->config['driver'] : '\\Lxh\\Session\\Driver\\' . ucwords($this->config['driver']);

        // 检查驱动类
        if (!class_exists($class) || !session_set_save_handler(new $class($this->config))) {
            throw new \Exception('error session handler:' . $class, $class);
        }
    }


    /**
     * session获取并删除
     * @param string        $name session名称
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name);

        if ($result) {
            $this->delete($name);
        }
    }

    /**
     * 保存session
     *
     * @param string $name
     * @param mixed  $value
     * @return bool
     */
    public function save($name = null, $value = null)
    {
        empty($this->init) && $this->boot();

        if ($name) {
            $this->$name = $value;

            $_SESSION[$name] = $value;
        } else {
            foreach ($this->toArray() as $k => & $item) {
                $_SESSION[$k] = $item;
            }
        }

        return true;
    }

    /**
     * 删除session数据
     * 支持删除多维模式："users.email"
     *
     * @param string|array  $name session名称
     * @param string|null   $prefix 作用域（前缀）
     * @return void
     */
    public function delete($name)
    {
        empty($this->init) && $this->boot();

        if (is_array($name)) {
            foreach ($name as & $key) {
                $this->delete($key);
            }
        } else {
            $this->remove($name);

            $_SESSION = Util::unsetInArray($_SESSION, $name);
        }
    }

    /**
     * 清空session数据
     * @return void
     */
    public function clear()
    {
        empty($this->init) && $this->boot();

        $_SESSION = [];
    }

    /**
     * 判断session数据
     *
     * @param string $name session名称
     * @return bool
     */
    public function has($name)
    {
        empty($this->init) && $this->boot();

        return parent::has($name);
    }

    /**
     * 添加数据到一个session数组
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function push($key, $value)
    {
        empty($this->init) && $this->boot();

        $this->append($key, $value);
    }

    /**
     * 销毁session
     * @return void
     */
    public function destroy()
    {
        empty($this->init) && $this->boot();

        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        $this->init = null;
    }

    public function __get($name)
    {
        empty($this->init) && $this->boot();

        return parent::__get($name); // TODO: Change the autogenerated stub
    }

    /**
     * 暂停session
     * @return void
     */
    public function pause()
    {
        empty($this->init) && $this->boot();

        // 暂停session
        session_write_close();
        $this->init = false;
    }
}
