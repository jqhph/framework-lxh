<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Form\Field;
use Lxh\Admin\Filter\Equal;
use Lxh\Admin\Filter\Gt;
use Lxh\Admin\Filter\Lt;
use Lxh\Admin\Filter\Between;
use Lxh\Admin\Filter\Where;
use Lxh\Admin\Filter\Like;
use Lxh\Admin\Filter\Ilike;

/**
 * Class DateRange.
 *
 * @method Equal equal()
 * @method Gt gt()
 * @method Lt lt()
 * @method Ilike ilike()
 * @method Like like()
 * @method Between between()
 * @method Where where(callable $callable)
 */
class DateRange extends Field
{
    use Condition;

    protected $view = 'admin::filter.date-range';

    protected $width = [
        'field' => 4
    ];

    /**
     * @var string
     */
    protected $defaultHandler = 'between';

    protected function setup()
    {
        $this->js('date-range', '@lxh/js/bootstrap-datetimepicker.min');
        $this->css('date-range',  '@lxh/css/bootstrap-datetimepicker.min');
    }

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
        $name = $this->name();

        $value = $this->setupValue($name);

        return array_merge(parent::variables(), [
            'start' => get_value($value, 'start', ''),
            'end' => get_value($value, 'end', ''),
            'startName' => $name . '-start',
            'endName' => $name . '-end',
        ]);
    }
    
    
}
