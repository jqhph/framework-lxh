<?php

namespace Lxh\Admin\Filter;

use Lxh\Admin\Filter;
use Lxh\Admin\Filter\Field\Select;
use Lxh\Admin\Filter\Field\Text;
use Lxh\Admin\Form\Field;
use Lxh\Template\View;

class AbstractFilter
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var string
     */
    protected $name = '';

    public function __construct($name, Field $field)
    {
        $this->name = $name;
        $this->field = $field;
    }

    /**
     * 过滤器条件表单名
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function render()
    {
        return "<input style='width:0;height:0;display:none' type='hidden' name='@{$this->name}[]' value='{$this->value()}' />";
    }

    /**
     * 过滤器值
     *
     * @return string
     */
    public function value()
    {
        return $this->field->name();
    }

    public function __toString()
    {
        return $this->render();
    }
}
