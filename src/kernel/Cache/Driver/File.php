<?php
/**
 * 文件缓存
 *
 * @author Jqh
 * @date   2017/6/16 17:41
 */

namespace Lxh\Cache\Driver;

use Lxh\Cache\Cache;

class File extends Cache
{
    public function save($key, $content)
    {
        return file_put_contents($key, $content, LOCK_EX) ? true : false;
    }

    public function get($key)
    {
        if (! is_file($key)) {
            return false;
        }
        return file_get_contents($key);
    }

    public function expiresAt($key, $date)
    {

    }

    public function expiresAfter($key, $time)
    {

    }
}
