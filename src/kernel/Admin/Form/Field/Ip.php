<?php

namespace Lxh\Admin\Form\Field;

class Ip extends Text
{
    protected static $js = [
        '@lxh/packages/input-mask/jquery.inputmask.bundle.min',
    ];

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'alias' => 'ip',
    ];

    public function render()
    {
        $options = json_encode($this->options);

        $this->script = "$('{$this->getElementClassSelector()}').inputmask($options);";

        $this->options = [];

        $this->prepend('<i class="fa fa-laptop"></i>');
        // ->defaultAttribute('style', 'width: 130px')

        return parent::render();
    }
}
