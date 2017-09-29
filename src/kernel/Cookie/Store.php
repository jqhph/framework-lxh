<?php

namespace Lxh\Cookie;

use Lxh\Helper\Entity;

class Store extends Entity
{
    protected $config = [
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ];

    protected $init;

    public function __construct()
    {
        $this->config = array_merge($this->config, (array) config('cookie'));

        $this->init();

        $this->items = & $_COOKIE;
    }

    public function setHttponly($val)
    {
        $this->config['httponly'] = $val;
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

    public function config()
    {
        return $this->config;
    }


    /**
     * Cookie初始化
     * @param array $config
     * @return void
     */
    protected function init()
    {
        if (!empty($this->config['httponly'])) {
            ini_set('session.cookie_httponly', 1);
        }
        $this->init = true;
    }


    /**
     * Cookie 设置、获取、删除
     *
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     *
     * @return mixed
     * @internal param mixed $options cookie参数
     */
    public function save($name, $value = '', $option = null)
    {
        // 参数设置(会覆盖黙认设置)
        if (!is_null($option)) {
            if (is_numeric($option)) {
                $option = ['expire' => $option];
            }

            $config = array_merge($this->config, (array) $option);

        } else {
            $config = & $this->config;
        }

        // 设置cookie
        $expire = !empty($config['expire']) ? $_SERVER['REQUEST_TIME'] + intval($config['expire']) : 0;

        if ($config['setcookie']) {
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }

        $_COOKIE[$name] = & $value;
    }

    /**
     * 永久保存Cookie数据
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public function forever($name, $value = '', $option = null)
    {
        if (is_null($option) || is_numeric($option)) {
            $option = [];
        }
        $option['expire'] = 315360000;

        $this->save($name, $value, $option);
    }

    /**
     * Cookie删除
     * 不支持多维删除
     *
     * @param string $name cookie名称
     * @return mixed
     */
    public function delete($name)
    {
        $config = & $this->config;

        if ($config['setcookie']) {
            setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }

        $this->remove($name);

        // 删除指定cookie
        unset($_COOKIE[$name]);
    }

    /**
     * Cookie清空
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public function clear()
    {
        // 清除指定前缀的所有cookie
        if (empty($_COOKIE)) {
            return;
        }

        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $config = &$this->config;

        // 如果前缀为空字符串将不作处理直接返回
        foreach ($_COOKIE as $key => & $val) {
            if ($config['setcookie']) {
                setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            }
            unset($_COOKIE[$key]);
        }

    }

}
