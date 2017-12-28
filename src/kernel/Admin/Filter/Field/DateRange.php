<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Form\Field;

class DateRange extends Field
{
    public static $js = [
        'bootstrap-datetimepicker.min'
    ];

    public static $css = [
        'bootstrap-datetimepicker.min'
    ];

    protected $view = 'admin::filter.date-range';

    protected $width = [
        'field' => 4
    ];

    protected function setupValue($name)
    {
        $value = [];
        if ($start = I($name . '-start', '')) {
            $value['start'] = $start;
        }
        if ($end = I($name . '-end', '')) {
            $value['end'] = $start;
        }

        if ($value) {
            $this->value = &$value;
        }

        return $this->value() ?: [];
    }


    protected function variables()
    {
        $name = $this->elementName ?: $this->formatName($this->column);

        $value = $this->setupValue($name);

        return array_merge(parent::variables(), [
            'start' => get_value($value, 'start', ''),
            'end' => get_value($value, 'end', ''),
            'startName' => $name . '-start',
            'endName' => $name . '-end'
        ]);
    }
    
    
}
