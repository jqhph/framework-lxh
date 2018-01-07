<?php

namespace Lxh\Support;

class Password
{
    /**
     * 加密密码
     *
     * @param $code
     * @return bool|string
     */
    public static function encrypt($code, $algo = PASSWORD_DEFAULT)
    {
        return password_hash($code, $algo);
    }

    /**
     * 验证密码
     *
     * @param $code
     * @param $hash
     * @return bool
     */
    public static function verify($code, $hash)
    {
        return password_verify($code, $hash);
    }
}
