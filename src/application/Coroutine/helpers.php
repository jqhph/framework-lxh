<?php

namespace Lxh\Coroutine;

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
            $task->setValue($scheduler->task($coroutine));
        }
    );
}

/**
 * 中断一个协程（在协程内使用）
 *
 * @param int $tid 协程ID
 * @return SystemCall
 */
function kill($tid)
{
    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($tid) {
            if (!$scheduler->kill($tid)) {
                throw new \InvalidArgumentException("Invalid task ID $tid!");
            }
        }
    );
}

/**
 * 获取协程ID
 *
 * @example $taskId = yield id();
 *
 * @return SystemCall
 */
function id()
{
    return new SystemCall(function(Task $task, Scheduler $scheduler) {
        $task->setValue($task->getId());
    });
}
