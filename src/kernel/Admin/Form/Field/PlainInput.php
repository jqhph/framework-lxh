<?php

namespace Lxh\Admin\Form\Field;

trait PlainInput
{
    protected $prepend;

    protected $append;

    public function prepend($string)
    {
        if (is_null($this->prepend)) {
            $this->prepend = $string;
        }

        return $this;
    }

    public function append($string)
    {
        if (is_null($this->append)) {
            $this->append = $string;
        }

        return $this;
    }

    protected function initPlainInput()
    {
        if (empty($this->view)) {
            $this->view = 'admin::form.input';
        }
    }

    protected function defaultAttribute($attribute, $value)
    {
        if (!isset($this->attributes[$attribute])) {
            $this->attribute($attribute, $value);
        }

        return $this;
    }
}
