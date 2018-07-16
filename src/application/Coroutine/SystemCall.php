<?php

namespace Lxh\Coroutine;

/**
 * 系统调用类
 *
 * Class SystemCall
 * @package Lxh\Coroutine
 */
class SystemCall
{
    /**
     * 如果在此回调函数内抛出异常，
     * 需要在Scheduler::run方法外才能捕获得到
     *
     * @var callable
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * 执行系统调用
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
