<?php

namespace Lxh\Admin\Fields\Traits;

use Lxh\Admin\Admin;

trait Builder
{
    /**
     * @var null
     */
    static $setGridEmailCss = null;

    protected function buildDate($name, $value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    protected function buildIcon($name, $value)
    {
        return $value ? "<i class=\"{$value}\"></i>" : '';
    }

    protected function buildSelect($name, $value)
    {
        return ($value !== '') ? trans_option($value, $name) : '';
    }

    protected function buildChecked($name, $value)
    {
        return $value ? '<i style="font-size:16px;font-weight:700" class="green zmdi zmdi-check"></i>'
            : '<i style="font-size:15px;" class="red zmdi zmdi-close"></i>';
    }

    protected function buildEmail($name, $value)
    {
        if (static::$setGridEmailCss === null) {
            static::$setGridEmailCss = 1;
            Admin::script('$(\'.grid-email\').css({color:\'#666\'});');
        }

        return ($value !== '' && $value !== null) ? '<i class="fa fa-envelope grid-email"></i> ' . $value : '';
    }

}
