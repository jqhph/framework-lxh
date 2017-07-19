<?php
/**
 * 后台url生成器
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 22:38
 */

namespace Lxh\Kernel;

class AdminUrlCreator
{
    protected static $prefix = 'lxhadmin';

    // 生成普通的url
    public static function makeAction($action = __ACTION__, $controller = __CONTROLLER__)
    {
        $pre = static::$prefix;

        return "/{$pre}/$controller/$action";
    }

    public static function makeHome()
    {
        return "/lxhadmin";
    }

    /**
     * 生成读取视图详情url
     */
    public static function makeDetail($id, $controller = __CONTROLLER__)
    {
        $pre = static::$prefix;

        return "/$pre/$controller/view/$id";
    }
}
