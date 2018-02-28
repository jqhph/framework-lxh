<?php

namespace Lxh\Admin\Form\Field;

class Color extends Text
{
    protected static $css = [
        '@lxh/packages/colorpicker/bootstrap-colorpicker.min',
    ];

    protected static $js = [
        '@lxh/packages/colorpicker/bootstrap-colorpicker.min',
    ];

    /**
     * Use `hex` format.
     *
     * @return $this
     */
    public function hex()
    {
        return $this->options(['format' => 'hex']);
    }

    /**
     * Use `rgb` format.
     *
     * @return $this
     */
    public function rgb()
    {
        return $this->options(['format' => 'rgb']);
    }

    /**
     * Use `rgba` format.
     *
     * @return $this
     */
    public function rgba()
    {
        return $this->options(['format' => 'rgba']);
    }

    /**
     * Render this filed.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $options = json_encode($this->options);

        $this->script = "$('{$this->getElementClassSelector()}').colorpicker($options);";

        $this->prepend('<i style="background:#222;width:11px;height:11px;display:inline-block"></i>');

        $this->options = [];

        return parent::render();
    }
}
