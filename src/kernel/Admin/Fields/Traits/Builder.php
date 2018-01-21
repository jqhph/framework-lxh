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
        return $value ? '<i style="font-size:16px;color:#10c469" class=" fa fa-check"></i>'
            : '<i style="color:#ff5b5b;font-size:16px;" class="zmdi zmdi-close-circle-o"></i>';
    }

}
