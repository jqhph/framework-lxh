<?php

namespace Lxh\Admin\Fields\Traits;

trait Builder
{
    protected function buildDate($name, $value, $vars)
    {
        return date('Y-m-d H:i:s', $value);
    }

    protected function buildIcon($name, $value, $vars)
    {
        return "<i class=\"{$value}\"></i>";
    }

    protected function buildEnum($name, $value, $vars)
    {
        return trans_option($value, $name);
    }

    protected function buildSelect($name, $value, $vars)
    {
        return trans_option($value, $name);
    }

}
