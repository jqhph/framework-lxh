<?php

namespace Lxh\Admin\Grid\Edit\Field;

class MultipleSelect extends Select
{
    protected $view = 'admin::filter.multiple-select';

    protected $width = ['field' => 4];

    /**
     * 是否允许清除单选框
     *
     * @var string
     */
    protected $clear = 'true';

    /**
     * Field default value.
     *
     * @var mixed
     */
    protected $default = [];

    protected function setupValue()
    {
        if ($this->value === false) return $this;
        // Field value is already setted.
        if (is_array($this->column)) {
            foreach ($this->column as $key => &$column) {
                $this->value[$key] = $this->items->get($column);
            }

            return $this;
        }

        $this->value = explode(',', $this->items->get($this->column));
    }

}
