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
        $this->handler('between');
        return $this;
    }

    public function gt()
    {
        $this->handler('gt');
        return $this;
    }

    public function lt()
    {
        $this->handler('lt');
        return $this;
    }

    public function like()
    {
        $this->handler('like');
        return $this;
    }

    public function rlike()
    {
        $this->handler('rlike');
        return $this;
    }

    public function ilike()
    {
        $this->handler('ilike');
        return $this;
    }

    public function equal()
    {
        $this->handler('equal');
        return $this;
    }

    public function where($call)
    {
        $this->handler('where', $call);
        return $this;
    }

    /**
     *
     * @param string $type
     * @return static
     */
    public function handler($type, $call = null)
    {
        $fieldName = $this->name();
        if (isset($this->handler[$fieldName])) {
            return $this;
        }
        $class = ucfirst($type);

        $class = "Lxh\\Admin\\Filter\\$class";

        return $this->handler[$fieldName] = new $class($type, $this, $call);
    }

    /**
     *
     * @return AbstractFilter
     */
    protected function getInputHandler()
    {
        $name = $this->name();

        $default = isset($this->defaultHandler) ? $this->defaultHandler : 'equal';

        return isset($this->handler[$name]) ? $this->handler[$name] : ($this->handler($default));
    }

}
