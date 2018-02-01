<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form;

class Checked extends Form\Field
{
    protected $view = 'admin::form.checked';

    protected function setup()
    {
        $this->css('checked', '@lxh/plugins/switchery/switchery.min');
        $this->js('checked', '@lxh/plugins/switchery/switchery.min');
        $this->script('checked', <<<EOF
 $('[data-plugin="switchery"]').each(function(){new Switchery($(this)[0],$(this).data())});
EOF
        );

        $this->primary();
    }

    public function primary()
    {
        return $this->attribute('data-color', '#00b19d');
    }

    public function info()
    {
        return $this->attribute('data-color', '#3bafda');
    }

    public function warning()
    {
        return $this->attribute('data-color', '#ffaa00');
    }

    public function inverse()
    {
        return $this->attribute('data-color', '#4c5667');
    }

    public function danger()
    {
        return $this->attribute('data-color', '#ef5350');
    }

    public function purple()
    {
        return $this->attribute('data-color', '#5b69bc');
    }

    /**
     *
     * @param $color
     * @return $this
     */
    public function secondary($color)
    {
        return $this->attribute('data-secondary-color', $color);
    }

    /**
     * @return $this
     */
    public function small()
    {
        return $this->attribute('data-size', 'small');
    }

    /**
     * @return $this
     */
    public function large()
    {
        return $this->attribute('data-size', 'large');
    }

    /**
     * @param $color
     * @return $this
     */
    public function color($color)
    {
        return $this->attribute('data-color', $color);
    }
}