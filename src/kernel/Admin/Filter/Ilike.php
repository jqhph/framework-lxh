<?php

namespace Lxh\Admin\Filter;

class Ilike extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name = '@ilike';

    protected $right = false;

    protected $left = false;

    protected function buildCondition($field)
    {
        $value = I($field);

        if ($this->left) {
            $p = $this->left ? "%$value" : "%$value%";
        } else {
            $p = $this->right ? "$value%" : "%$value%";
        }

        return ($value === '' || $value === null) ? null : ['ilike', &$p];
    }


    public function left()
    {
        $this->left = true;

        return $this;
    }

    /**
     * 右边like
     *
     * @return static
     */
    public function right()
    {
        $this->right = true;

        return $this;
    }
}
