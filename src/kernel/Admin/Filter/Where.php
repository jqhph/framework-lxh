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

    public function __construct(Field $field, callable $callback)
    {
        parent::__construct($field);

        $this->callback = $callback;
    }

    public function render()
    {
        return call_user_func($this->callback, $this, $this->field);
    }
}
