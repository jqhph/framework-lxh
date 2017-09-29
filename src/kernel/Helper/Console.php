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

    public static function group()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'group'];
    }

    public static function groupCollapsed()
    {
        self::$records[] = ['args' => func_get_args(), 'type' => 'groupCollapsed'];
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

    public static function __callStatic($name, $arguments)
    {
        self::$records[] = ['args' => & $arguments, 'type' => $name];
    }

    /**
     * 获取console字符串
     *
     * @return string
     */
    public static function fetch()
    {
        if (empty(static::$records)) return '';

        $txt = 'console.group("%c FROM SERVER ", "color:red;font-weight:bold"); ';

        foreach (static::$records as & $content) {
           $txt .= static::fetchRow($content);
        }

        $txt .= 'console.log("%cEND", "color:red;font-weight:bold");console.groupEnd();';

        static::$records = [];

        return "<script type='text/javascript'>$txt</script>";
    }

    protected static function fetchRow(array & $content)
    {
        $type = $content['type'];
        $txt = "console.$type(";

        foreach ($content['args'] as & $log) {
            if (is_array($log)) {
                $txt .= json_encode($log) . ', ';
            } else if (is_int($log) || is_float($log) || is_bool($log)) {
                $txt .= "$log,";
            } else {
                // 转义单引号，换行符
                $log = str_replace(["'", "\n", "\r"], ["\\'", '', ''], (string) $log);
                $txt .= "'$log',";
            }
        }
        return rtrim($txt, ', ') . '); ';
    }
}
