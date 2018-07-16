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
     * 返回值会作为结果被返回
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
     * 返回值会作为结果被返回
     *
     * @param Task $task
     * @param Scheduler $scheduler
     * @return mixed 返回false会中断协程任务
     */
    public function call(Task $task, Scheduler $scheduler)
    {
        return call_user_func($this->callback, $task, $scheduler);
    }

}
