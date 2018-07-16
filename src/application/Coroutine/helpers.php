<?php

namespace Coroutine;

use Lxh\Coroutine\Value;
use Lxh\Coroutine\Task;
use Lxh\Coroutine\Scheduler;
use Lxh\Coroutine\SystemCall;
use Generator;

/**
 * 协程的返回值
 *
 * @param mixed $value
 * @return Value
 */
function value($value)
{
    return new Value($value);
}

/**
 * 创建一个子协程任务（在协程内使用）
 * 返回任务ID
 *
 * @param Generator $coroutine
 * @return SystemCall
 */
function task(Generator $coroutine) {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) use ($coroutine) {
            return $scheduler->task($coroutine);
        }
    );
}

/**
 * 中断一个协程（在协程内使用）
 *
 * 成功返回true，失败返回false
 *
 * @param int $tid 协程ID
 * @return SystemCall
 */
function kill($tid)
{
    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($tid) {
            return $scheduler->kill($tid);
        }
    );
}

/**
 * 获取协程任务ID
 *
 * @example $taskId = yield id();
 *
 * @return SystemCall
 */
function id()
{
    return new SystemCall(function(Task $task, Scheduler $scheduler) {
        return $task->getId();
    });
}
