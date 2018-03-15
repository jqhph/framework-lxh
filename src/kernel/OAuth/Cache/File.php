<?php

namespace Lxh\OAuth\Cache;

use Lxh\Cache\File as FileDriver;

class File extends Cache
{
    /**
     * @var FileDriver
     */
    protected $driver;

    public function __construct()
    {
        $this->driver = new FileDriver('_oauth_');
    }

    public function set($key, $value, $life = 0)
    {
        if (empty($key)) return false;

        return $this->driver->set($this->normalizeKey($key), $value, $life);
    }

    public function get($key)
    {
        if (empty($key)) return false;

        return $this->driver->get($this->normalizeKey($key));
    }

    public function delete($key)
    {
        if (empty($key)) return false;

        return $this->driver->delete($this->normalizeKey($key));
    }

}
