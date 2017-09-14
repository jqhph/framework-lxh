<?php

namespace Lxh\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \Lxh\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
