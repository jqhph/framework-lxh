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
    return make('page');
}



/**
 * 权限管理
 *
 * @return Permit
 */
function acl()
{
    static $instance = null;

    return $instance ?: ($instance = new Permit());
}

/**
 * 缓存
 *
 * @return Cache
 */
function cache($key = null)
{
    static $instances = [];

    $key = $key ?: config('cache-driver', 'File');

    if (isset($instances[$key])) return $instances[$key];

    $class = "\\Lxh\\Kernel\\Cache\\{$key}";

    return $instances[$key] = new $class();
}
