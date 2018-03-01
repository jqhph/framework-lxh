<?php

namespace Lxh\Admin\Form\Field;

class Datetime extends Date
{
    protected $options = [
        'format' => 'yy-mm-dd hh:ii:ss',
        'locale' => 'zh-CH',
    ];
    
}
