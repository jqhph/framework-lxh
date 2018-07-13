<?php

namespace Lxh\Coroutine;

class SystemCall
{
    /**
     * @var callable
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * 调用系统调用
     *
     * @param Task $task
     * @param Scheduler $scheduler
     * @return mixed
     */
    protected function call(Task $task, Scheduler $scheduler)
    {
        return call_user_func($this->callback, $task, $scheduler);
    }

    public function __invoke(Task $task, Scheduler $scheduler) {
        return $this->call($task, $scheduler);
    }

}
