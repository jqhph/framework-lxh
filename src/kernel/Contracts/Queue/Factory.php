<?php

namespace Lxh\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string  $name
     * @return \Lxh\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
