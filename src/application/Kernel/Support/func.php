<?php
/**
 * 公共业务函数
 *
 * @author Jqh
 * @date   2017/6/15 15:17
 */

use Lxh\Admin\Acl\Permit;
use Lxh\Kernel\Support\Page;

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
function permit()
{
    static $instance = null;

    return $instance ?: ($instance = new Permit());
}
