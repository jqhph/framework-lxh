<?php

namespace Lxh\Contracts\Queue;

interface QueueableEntity
{
    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection();
}
