<?php

namespace Lxh\Coroutine;

use Generator;
use SplQueue;
use Exception;

/**
 * 协程调度器
 *
 * Class Scheduler
 * @package Lxh\Coroutine
 */
class Scheduler
{
    /**
     * 任务ID
     *
     * @var int
     */
    protected $lastTaskId = 0;

    /**
     * taskId => task
     *
     * @var array
     */
    protected $taskMap = [];

    /**
     * 任务队列
     *
     * @var SplQueue
     */
    protected $taskQueue;

    public function __construct()
    {
        $this->taskQueue = new SplQueue();
    }

    /**
     * 添加一个协程任务
     *
     * @param Generator $coroutine
     * @return int 返回协程ID
     */
    public function task(Generator $coroutine)
    {
        $tid  = ++$this->lastTaskId;
        $task = new Task($tid, $coroutine);

        $this->taskMap[$tid] = $task;
        $this->push($task);

        return $tid;
    }

    /**
     * 结束task任务
     *
     * @param int $tid
     * @return bool
     */
    public function kill($tid)
    {
        if (!isset($this->taskMap[$tid])) {
            return false;
        }

        unset($this->taskMap[$tid]);

        /* @var Task $task */
        // This is a bit ugly and could be optimized so it does not have to walk the queue,
        // but assuming that killing tasks is rather rare I won't bother with it now
        foreach ($this->taskQueue as $i => $task) {
            if ($task->getId() === $tid) {
                unset($this->taskQueue[$i]);
                break;
            }
        }

        return true;
    }

    /**
     * 把协程任务置入调度队列
     *
     * @param Task $task
     * @return $this
     */
    public function push(Task $task)
    {
        $this->taskQueue->push($task);
        return $this;
    }

    /**
     * 开始调度任务
     */
    public function run()
    {
        while (!$this->taskQueue->isEmpty()) {
            /* @var Task $task */
            $task  = $this->taskQueue->dequeue();
            $value = $task->run();

            // 如果是系统调用器
            if ($value instanceof SystemCall) {
                try {
                    if ($value->call($task, $this) !== false) {
                        $this->push($task);
                    }
                } catch (Exception $e) {
                    $task->throwException($e);
                }
                continue;
            }

            if ($task->isFinished()) {
                unset($this->taskMap[$task->getId()]);
            } else {
                $this->push($task);
            }
        }
    }

}
