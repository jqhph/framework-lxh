<?php

namespace Lxh\Basis;

use Lxh\Contracts\Container\Container;

abstract class Factory
{
    protected $container;

    protected $defaultName;

    protected $instances = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 获取对象实例
     * */
    final public function get($name = null)
    {
        if (! $name) {
            $name = & $this->defaultName;
        }
        if (! isset($this->instances[$name])) {
            $this->instances[$name] = $this->create($name);
        }
        return $this->instances[$name];
    }

    /**
     * 生产一个实例
     *
     * @return instance
     * */
    abstract public function create($name);

    protected function getContainer()
    {
        return $this->container;
    }

    public function getInstancesKeys()
    {
        return array_keys($this->instances);
    }

}
