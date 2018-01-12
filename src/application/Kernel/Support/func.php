<?php
/**
 * 公共业务函数
 *
 * @author Jqh
 * @date   2017/6/15 15:17
 */

use Lxh\Admin\Acl\Permit;
use Lxh\Kernel\Support\Page;
use Lxh\Kernel\Cache\Cache;

/**
 * @return Page
 */
function pages()
{
    return resolve('page');
}

/**
 * 缓存
 *
 * @param string $name 名称。当缓存类型为文件时，此参数表示缓存目录
 * @param string $driver 缓存类型，File
 * @return Cache
 */
function cache($name = '', $driver = null)
{
    static $instances = [];

    $driver = $driver ?: config('cache-driver', 'File');

    $key = $name . $driver;

    if (isset($instances[$key])) return $instances[$key];

    $class = "\\Lxh\\Kernel\\Cache\\{$driver}";

    return $instances[$key] = new $class($name);
}
