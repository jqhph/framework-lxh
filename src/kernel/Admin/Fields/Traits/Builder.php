<?php

namespace Lxh\Admin\Fields\Traits;

trait Builder
{
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

}
