<?php

namespace Lxh\Admin\Grid\Filter;

class Gt extends AbstractFilter
{
    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition($inputs)
    {
        $value = get_value($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '>=', $this->value);
    }
}
