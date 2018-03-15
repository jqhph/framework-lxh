<?php

/**
 * 加密密码
 *
 * @param $code
 * @return bool|string
 */
function encrypt($code, $algo = PASSWORD_DEFAULT)
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
function verify($code, $hash)
{
    return password_verify($code, $hash);
}

//$token = password_hash('test', PASSWORD_DEFAULT, ['salt' => md5(time())]);
//
//var_dump(password_verify('test', $token));

$time = time();

$a = hash('sha256', 'test'.$time);

var_dump($a);
var_dump($a == hash('sha256', 'test'.$time));