<?php

namespace Lxh\Coroutine;

use Generator;
use SplStack;

/**
 * 协程任务
 *
 * Class Task
 * @package Lxh\Coroutine
 */
class Task
{
    /**
     * 任务ID
     *
     * @var int
     */
    protected $id;

    /**
     * 迭代生成器
     *
     * @var Generator
     */
    protected $coroutine;

    /**
     * 发送到下次恢复的值
     *
     * @var mixed
     */
    protected $value;

    /**
     * 是否是第一次执行yield
     *
     * @var bool
     */
    protected $beforeFirstYield = true;

    public function __construct($id, Generator $coroutine)
    {
        $this->id        = $id;
        $this->coroutine = $this->stackedCoroutine($coroutine);
    }

    /**
     * 获取任务ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 设置发送到下次恢复的值
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = &$value;
        return $this;
    }

    /**
     * 协程栈
     *
     * @param Generator $coroutine
     * @return Generator
     */
    protected function stackedCoroutine(Generator $coroutine) {
        $stack = new SplStack;

        for (;;) {
            $value = $coroutine->current();

            if ($value instanceof Generator) {
                $stack->push($coroutine);
                $coroutine = $value;
                continue;
            }

            $isCoroutineWrapper = $value instanceof Wrapper;
            if (!$coroutine->valid() || $isCoroutineWrapper) {
                if ($stack->isEmpty()) {
                    return;
                }

                $coroutine = $stack->pop();
                $coroutine->send($isCoroutineWrapper ? $value->getValue() : null);
                continue;
            }

            $coroutine->send(yield $coroutine->key() => $value);
        }
    }

    /**
     * 处理任务
     *
     * @return mixed
     */
    public function run()
    {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        }
        $result = $this->coroutine->send($this->value);
        $this->value = null;
        return $result;

    }

    /**
     * 判断任务是否完成
     *
     * @return bool
     */
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }

}
