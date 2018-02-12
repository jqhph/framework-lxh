<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

/**
 * Class Td
 * @package Lxh\Admin\Table
 * @method void class($class)
 */
class Td extends Widget
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value = null)
    {
        $this->value = &$value;
    }

    /**
     * @param mixed $value
     * @return $this|null
     */
    public function value($value = null)
    {
        if ($value === null) {
            return $this->value;
        }

        $this->value = &$value;
        return $this;
    }

    public function render()
    {
        return "<td {$this->formatAttributes()}>{$this->value}</td>";
    }
}
