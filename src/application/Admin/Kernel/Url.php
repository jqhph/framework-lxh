<?php
/**
 * 后台url生成器
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 22:38
 */

namespace Lxh\Admin\Kernel;

use Lxh\Helper\Util;

class Url
{
    protected static $prefix = 'admin';

    // 生成普通的url
    public static function makeAction($action = __ACTION__, $controller = __CONTROLLER__)
    {
        $pre = static::$prefix;

        $action = Util::toUnderScore($action, true, '-');

        $controller = Util::toUnderScore($controller, true, '-');

        return "/{$pre}/$controller/$action";
    }

    public static function makeHome()
    {
        return "/admin";
    }

    /**
     * 生成读取视图详情url
     */
    public static function makeDetail($id, $controller = __CONTROLLER__)
    {
        $pre = static::$prefix;

        $controller = Util::toUnderScore($controller, true, '-');

        return "/$pre/$controller/view/$id";
    }
}
