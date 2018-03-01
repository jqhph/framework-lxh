<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;

class Slider extends Field
{
    protected static $css = [
        '@lxh/packages/ionslider/ion.rangeSlider',
        '@lxh/packages/ionslider/ion.rangeSlider.skinNice',
    ];

    protected static $js = [
        '@lxh/packages/ionslider/ion.rangeSlider.min',
    ];

    protected $view = 'admin::form.slider';

    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    /**
     * 设置滑动条最小值
     *
     * @param int $min
     * @return $this
     */
    public function min($min)
    {
        return $this->attribute('min', $min);
    }

    /**
     * 设置滑动条最大值
     *
     * @param int $max
     * @return $this
     */
    public function max($max)
    {
        return $this->attribute('max', $max);
    }

    /**
     * 设置滑动条步长
     *
     * @param int $step
     * @return $this
     */
    public function step($step)
    {
        return $this->attribute('step', $step);
    }

    public function render()
    {
        $option = json_encode($this->options);

        $this->script = "$('{$this->getElementClassSelector()}').ionRangeSlider($option)";

        return parent::render();
    }
}
