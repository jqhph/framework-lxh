<?php

namespace Lxh\Admin\Filter;

use Lxh\Admin\Form\Field;

/**
 * 自定义条件过滤
 * 
 */
class Where extends AbstractFilter
{
    protected $callback;

    public function __construct($name, Field $field, callable $callback)
    {
        parent::__construct($name, $field);

        $this->callback = $callback;
    }

    public function render()
    {
        return call_user_func($this->callback, $this, $this->field);
    }
}
