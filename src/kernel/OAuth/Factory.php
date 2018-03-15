<?php

namespace Lxh\OAuth;

class Factory
{
    protected static $instances = [];

    /**
     * @param string $key
     * @param array $options
     * @return User
     */
    public static function resolve($key, array $options = [])
    {
        if (isset(static::$instances[$key])) {
            return static::$instances[$key];
        }

        if (! $options) {
            $options = config('oauth.'. $key);
        }

        return static::$instances[$key] = new User($options);
    }

    /**
     * 前台用户
     *
     * @param bool $isOpen
     * @return User
     */
    public static function user(array $options = [])
    {
        return static::resolve('user', $options);
    }

    /**
     * 后台用户
     *
     * @param array $options
     * @return User
     */
    public static function admin(array $options = [])
    {
        return static::resolve('admin', $options);
    }
}
