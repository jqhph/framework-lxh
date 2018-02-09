<?php

namespace Lxh\Basis;

use Lxh\Contracts\Container\Container;
use ArrayAccess;

abstract class Factory implements ArrayAccess
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $defaultName;

    /**
     * @var array
     */
    protected $instances = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 获取对象实例
     *
     * @param null $name
     * @return mixed
     */
    final public function get($name = null)
    {
        if (! $name) {
            $name = $this->getDefaultName();
        }
        if (! isset($this->instances[$name])) {
            $this->instances[$name] = $this->create($name);
        }
        return $this->instances[$name];
    }

    /**
     * 生产一个实例
     *
     * @return object
     * */
    abstract public function create($name);

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->instances);
    }

    public function __get($name)
    {
        if (! isset($this->instances[$name])) {
            $this->instances[$name] = $this->create($name);
        }
        return $this->instances[$name];
    }

    /**
     * 设置默认名称
     *
     * @param string $name
     * @return $this
     */
    public function setDefaultName($name)
    {
        $this->defaultName = &$name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return $this->defaultName;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->instances[$key]);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (! isset($this->instances[$key])) {
            $this->instances[$key] = $this->create($key);
        }
        return $this->instances[$key];
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->instances[$key] = $value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->instances[$key]);
    }

}
