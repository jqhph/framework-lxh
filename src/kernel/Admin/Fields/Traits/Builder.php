<?php

namespace Lxh\Admin\Fields\Traits;

use Lxh\Admin\Admin;

trait Builder
{
    /**
     * @var null
     */
    static $setGridEmailStyle = null;

    /**
     * 时间戳转日期
     *
     * @param $name
     * @param $value
     * @return bool|string
     */
    protected function buildDate($name, $value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 字体图标
     *
     * @param $name
     * @param $value
     * @return string
     */
    protected function buildIcon($name, $value)
    {
        return $value ? "<i class=\"{$value}\"></i>" : '';
    }

    /**
     * 选项翻译
     *
     * @param $name
     * @param $value
     * @return int|string
     */
    protected function buildSelect($name, $value)
    {
        return ($value !== '' && $value !== null) ? trans_option($value, $name) : '';
    }

    protected static $checkedStyle = false;
    /**
     * 选中效果
     *
     * @param $name
     * @param $value
     * @return string
     */
    protected function buildChecked($name, $value)
    {
        if (static::$checkedStyle === false) {
            static::$checkedStyle = true;
            Admin::style('.grid-checked{font-size:17px;}.grid-unchecked{font-size:17px;}');
        }

        return $value ? '<i class="green grid-checked zmdi zmdi-check"></i>'
            : '<i class="red grid-unchecked zmdi zmdi-close"></i>';
    }

    /**
     * 邮箱
     *
     * @param $name
     * @param $value
     * @return string
     */
    protected function buildEmail($name, $value)
    {
        if (static::$setGridEmailStyle === null) {
            static::$setGridEmailStyle = 1;
            Admin::style('.grid-email{color:#666}');
        }

        return ($value !== '' && $value !== null) ? '<i class="fa fa-envelope grid-email"></i> ' . $value : '';
    }

    public function buildIp($name, $value)
    {
        return $value ? '<i class="fa fa-laptop"></i> ' . long2ip($value) : '';
    }

}
