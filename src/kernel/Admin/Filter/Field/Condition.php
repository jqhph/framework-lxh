<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Filter\AbstractFilter;

trait Condition
{
    /**
     * @var AbstractFilter
     */
    protected $handler = [];

    public function between()
    {
        $this->condition('between');
        return $this;
    }

    public function gt()
    {
        $this->condition('gt');
        return $this;
    }

    public function lt()
    {
        $this->condition('lt');
        return $this;
    }

    public function like()
    {
        $this->condition('like');
        return $this;
    }

    public function rlike()
    {
        $this->condition('rlike');
        return $this;
    }

    public function ilike()
    {
        $this->condition('ilike');
        return $this;
    }

    public function equal()
    {
        $this->condition('equal');
        return $this;
    }

    public function where($call)
    {
        $this->condition('where', $call);
        return $this;
    }

    /**
     *
     * @param string $type
     * @return static
     */
    public function condition($type, $call = null)
    {
        $fieldName = $this->name();
        if (isset($this->handler[$fieldName])) {
            return $this;
        }
        $class = ucfirst($type);

        $class = "Lxh\\Admin\\Filter\\$class";

        return $this->handler[$fieldName] = new $class($this, $call);
    }

    /**
     *
     * @return AbstractFilter
     */
    protected function getInputHandler()
    {
        $name = $this->name();

        $default = isset($this->defaultHandler) ? $this->defaultHandler : 'equal';

        return isset($this->handler[$name]) ? $this->handler[$name] : ($this->condition($default));
    }

}
