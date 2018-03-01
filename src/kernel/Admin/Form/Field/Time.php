<?php

namespace Lxh\Admin\Form\Field;

class Time extends Date
{
    /**
     *
     * @var array
     */
    protected $options = [
        'format' => 'hh:ii:ss',
        'locale' => 'zh-CH',
        'startView' => 'hour',
    ];

    public function render()
    {
        $this->prepend('<i class="fa fa-clock-o"></i>');

        return parent::render();
    }
}
