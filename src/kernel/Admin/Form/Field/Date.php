<?php

namespace Lxh\Admin\Form\Field;

class Date extends Text
{
    protected static $css = [
        '@lxh/css/bootstrap-datetimepicker.min',
    ];

    protected static $js = [
        '@lxh/js/bootstrap-datetimepicker.min',
    ];

    /**
     * 
     * @var array
     */
    protected $options = [
        'format' => 'yy-mm-dd',
        'locale' => 'zh-CH',
        'minView' => 'month',
    ];

    public function format($format)
    {
        $this->options['format'] = $format;

        return $this;
    }

    /**
     * 设置语言
     *
     * @param string $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->options['locale'] = $locale;

        return $this;
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
        $this->script = "$('{$this->getElementClassSelector()}').datetimepicker(".json_encode($this->options).');';

        $this->options = [];

        $this->prepend('<i class="fa fa-calendar"></i>');

        return parent::render();
    }
}
