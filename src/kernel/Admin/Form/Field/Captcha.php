<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form;

class Captcha extends Text
{
    /**
     *
     * @var string
     */
    protected $view = 'admin::form.captcha';

    public function __construct($column, $arguments = [])
    {
        if (!class_exists(\Mews\Captcha\Captcha::class)) {
            throw new \Exception('To use captcha field, please install [mews/captcha] first.');
        }

        $this->column = '__captcha__';
        $this->label = trans('admin::lang.captcha');
    }

    public function render()
    {
        $this->script = <<<EOT

$('#{$this->column}-captcha').click(function () {
    $(this).attr('src', $(this).attr('src')+'?'+Math.random());
});
EOT;

        return parent::render();
    }
}
