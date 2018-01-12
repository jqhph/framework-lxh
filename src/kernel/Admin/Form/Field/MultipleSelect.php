<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Support\Arr;

class MultipleSelect extends Select
{
    protected $view = 'admin::form.multiple-select';

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

    public function fill($data)
    {
        $this->row = &$data;
        // Field value is already setted.
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->value[$key] = get_value($data, $column);
            }

            return;
        }

        $this->value = (array) get_value($data, $this->column);
    }
}
