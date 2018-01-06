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

    protected function buildCondition($field, $input)
    {
        if ($this->left) {
            $p = $this->left ? "%$input" : "%$input%";
        } else {
            $p = $this->right ? "$input%" : "%$input%";
        }

        return ['ilike', &$p];
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
