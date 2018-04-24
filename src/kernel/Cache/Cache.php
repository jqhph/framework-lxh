<?php

namespace Lxh\Cache;

abstract class Cache
{
    abstract public function set($key, $content, $timeout = 0);

    abstract public function get($key);

    abstract public function delete($key);

    abstract public function expiresAt($key, $date);

    abstract public function expiresAfter($key, $time);

    abstract public function flush($type = null);

    abstract public function setType($type);

    abstract public function getType();
}
