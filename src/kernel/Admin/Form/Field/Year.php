<?php

namespace Lxh\Admin\Form\Field;

class Year extends Date
{
    /**
     *
     * @var array
     */
    protected $options = [
        'format' => 'yyyy',
        'locale' => 'zh-CH',
        'startView' => '4',
        'minView' => '4',
    ];
}
