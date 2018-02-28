<?php

namespace Lxh\Admin\Form\Field;

class Icon extends Text
{
    protected $default = 'fa-pencil';

    protected static $css = [
        '@lxh/packages/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min',
    ];

    protected static $js = [
        '@lxh/packages/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min',
    ];

    public function render()
    {
        $this->script = "$('{$this->getElementClassSelector()}').iconpicker({placement:'bottomLeft'});";

        $this->prepend('<i class="fa fa-pencil"></i>');

        return parent::render();
    }
}
