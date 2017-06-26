<?php
/**
 * 输出内容到web控制台
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/25
 * Time: 0:13
 */

namespace Lxh\Helper;

class Console
{
    protected static $records = [];

    /**
     * console.log
     *
     * @return void
     */
    public static function log()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'log'];
    }

    /**
     * console.info
     *
     * @return void
     */
    public static function info()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'info'];
    }

    /**
     * console.warn
     *
     * @return void
     */
    public static function warn()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'warn'];
    }

    /**
     * console.error
     *
     * @return void
     */
    public static function error()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'error'];
    }

    /**
     * console.table
     *
     * @return void
     */
    public static function table()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'table'];
    }

    /**
     * 获取console字符串
     *
     * @return string
     */
    public static function fetch()
    {
        $txt = 'console.log("%c-------------------------------------------------- FROM SERVER -----------------------------------------------------------", "color:red;font-weight:bold"); ';

        foreach (self::$records as & $content) {
           $txt .= static::fetchRow($content);
        }

        $txt .= 'console.log("%c-------------------------------------------------- END -------------------------------------------------------------------", "color:red;font-weight:bold"); ';
        return "<script type='text/javascript'>$txt</script>";
    }

    protected static function fetchRow(array & $content)
    {
        $type = $content['type'];
        $txt = "console.$type(";

        foreach ($content['args'] as & $log) {
            if (is_array($log)) {
                $txt .= json_encode($log) . ', ';
            } else if (is_int($log) || is_float($log)) {
                $txt .= "$log,";
            } else {
                $txt .= "'$log',";
            }
        }
        return rtrim($txt, ', ') . '); ';
    }
}
