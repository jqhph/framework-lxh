<?php

namespace Lxh\Cache;

abstract class Cache
{
    abstract public function save($key, $content);

    abstract public function get($key);

    abstract public function expiresAt($key, $date);

    abstract public function expiresAfter($key, $time);
}
