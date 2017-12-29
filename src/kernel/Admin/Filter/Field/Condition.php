<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Filter\AbstractFilter;
use Lxh\Admin\Filter\Where;

trait Condition
{
    /**
     * @var AbstractFilter
     */
    protected $conditions = [];

    public function between()
    {
        return $this->condition('between');
    }

    public function gt()
    {
        return $this->condition('gt');
    }

    public function lt()
    {
        return $this->condition('lt');
    }

    public function like()
    {
        return $this->condition('like');
    }

    public function rlike()
    {
        return $this->condition('rlike');
    }

    public function ilike()
    {
        return $this->condition('ilike');
    }

    public function equal()
    {
        return $this->condition('equal');
    }

    /**
     * @param $call
     * @return Where
     */
    public function where(callable $call)
    {
        return $this->condition('where', $call);
    }

    /**
     *
     * @param string $type
     * @return static
     */
    public function condition($type = null, $call = null)
    {
        $fieldName = $this->name();
        if (isset($this->conditions[$fieldName])) {
            return $this->conditions[$fieldName];
        }

        if (! $type) {
            $type = isset($this->defaultHandler) ? $this->defaultHandler : 'equal';
        }

        $class = ucfirst($type);

        $class = "Lxh\\Admin\\Filter\\$class";

        $this->conditions[$fieldName] = new $class($this, $call);
        
        $this->filter()->condition($this->conditions[$fieldName]);

        return $this->conditions[$fieldName];
    }

}
