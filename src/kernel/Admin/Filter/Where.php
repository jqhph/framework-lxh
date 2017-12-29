<?php

namespace Lxh\Admin\Filter;

use Lxh\Admin\Form\Field;

/**
 * 自定义条件过滤器
 * 
 */
class Where extends AbstractFilter
{
    protected $renderCallable;

    public function __construct(Field $field, callable $renderCallable)
    {
        parent::__construct($field);

        $this->renderCallable = $renderCallable;
    }

    public function render()
    {
        return call_user_func($this->renderCallable, $this, $this->field);
    }

    protected function buildCondition($field)
    {
    }
}
