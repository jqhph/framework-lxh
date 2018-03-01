<?php

namespace Lxh\Admin\Form\Field;

class DateTimeRange extends DateRange
{
    /**
     *
     * @var array
     */
    protected $options = [
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'locale' => 'zh-CH',
    ];
}
