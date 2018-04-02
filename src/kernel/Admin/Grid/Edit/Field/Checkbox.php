<?php

namespace Lxh\Admin\Grid\Edit\Field;

class Checkbox extends Radio
{
    protected $inline = 'checkbox-inline';

    protected $type = 'checkbox';

    public function value($value = null)
    {
        if (is_null($value)) {
            return is_null($this->value) ? $this->getDefault() : $this->value;
        }

        if (is_array($value)) {
            $this->value = &$value;
        } else {
            $this->value = explode(',', $value);
        }

        return $this;
    }

    protected function variables()
    {
        $this->value = (array)$this->value;

        return parent::variables(); // TODO: Change the autogenerated stub
    }
}