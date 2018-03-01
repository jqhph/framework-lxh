<?php

namespace Lxh\Admin\Form\Field;

class Month extends Date
{
    protected $options = [
        'format' => 'mm',
        'locale' => 'zh-CH',
        'startView' => 'year',
        'minView' => 'year',
    ];
}
