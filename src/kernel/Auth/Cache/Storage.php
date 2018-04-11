<?php

namespace Lxh\Auth\Cache;

class Storage
{
    protected $driver;

    public function __construct($driver = null)
    {
        $this->driver = $driver;

        if (method_exists($driver, 'setType')) {
            $driver->setType('__auth__');
        }
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function put($key, $value, $timeout = 0)
    {
        if (empty($key)) {
            return false;
        }
        return $this->driver->set($key, $value, $timeout);
    }

    public function get($key)
    {
        return $this->driver->get($key);
    }

    public function flush()
    {
        if (method_exists($this->driver, 'flush')) {
            $this->driver->flush();
        }
        if (method_exists($this->driver, 'flush')) {
            $this->driver->flush();
        }
        return true;
    }

    public function forget($key)
    {
        return $this->driver->delete($key);
    }

    public function forever($key, $value)
    {
        return $this->put($key, $value);
    }

}
