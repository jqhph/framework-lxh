<?php

namespace Lxh\Admin\Form\Field;

class Mobile extends Text
{
//    protected static $js = [
//        '@lxh/packages/input-mask/jquery.inputmask.bundle.min',
//    ];

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'mask' => '99999999999',
    ];

    public function render()
    {
//        $options = json_encode($this->options);
//        $this->script = "$('{$this->getElementClassSelector()}').inputmask($options);";

        $this->prepend('<i class="fa fa-phone"></i>');

        $this->options = [];

        return parent::render();
    }
}
