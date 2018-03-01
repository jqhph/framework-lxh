<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;

class DateRange extends Field
{
    protected static $css = [
        '@lxh/css/bootstrap-datetimepicker.min',
    ];

    protected static $js = [
        '@lxh/js/bootstrap-datetimepicker.min',
    ];

    protected $view = 'admin::form.date-range';

    /**
     *
     * @var array
     */
    protected $options = [
        'format' => 'yy-mm-dd',
        'locale' => 'zh-CH',
        'minView' => 'month',
    ];

    /**
     * Column name.
     *
     * @var string
     */
    protected $column = [];

    public function __construct($column, $arguments)
    {
        $this->column['start'] = $column;
        $this->column['end'] = $arguments[0];

        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        $this->options(['format' => $this->format]);
    }

    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['locale'] = config('app.locale');

        $startOptions = json_encode($this->options);
        $endOptions = json_encode($this->options + ['useCurrent' => false]);

        $class = $this->getElementClassSelector();

        $this->script = <<<EOT
            $('{$class['start']}').datetimepicker($startOptions);
            $('{$class['end']}').datetimepicker($endOptions);
            $("{$class['start']}").on("dp.change", function (e) {
                $('{$class['end']}').data("DateTimePicker").minDate(e.date);
            });
            $("{$class['end']}").on("dp.change", function (e) {
                $('{$class['start']}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        return parent::render();
    }
}
