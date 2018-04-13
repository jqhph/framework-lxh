<?php

namespace Lxh\RequestAuth\Cache;

use Lxh\Cache\File as FileDriver;

class File extends Cache
{
    /**
     * @var FileDriver
     */
    protected $driver;

    public function __construct()
    {
        $this->driver = new FileDriver('_reqat_');
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

    /**
     * 追加数据到缓存
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function append($key, $value)
    {
        if (empty($key)) return false;

        $data = $this->getArray($key);

        $data[] = &$value;

        return $this->set($key, $data);
    }

    public function getArray($key)
    {
        if (empty($key)) return false;

        return (array)$this->driver->get($this->normalizeKey($key));
    }

    public function deleteInArray($key, $value)
    {
        if (empty($key)) return false;

        if (! $data = $this->getArray($key)) {
            return false;
        }

        foreach ($data as $k => &$val) {
            if ($value == $val) {
                unset($data[$k]);
                break;
            }
        }

        return $this->set($key, $data);
    }

}
