<?php

namespace Lxh\Coroutine;

use Generator;
use SplStack;
use Exception;

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
    protected $co;

    /**
     * 发送到下次恢复的值
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * 是否是第一次执行yield
     *
     * @var bool
     */
    protected $beforeFirstYield = true;

    public function __construct($id, Generator $coroutine)
    {
        $this->id = $id;
        $this->co = $this->stackedCoroutine($coroutine);
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
     * @param Exception $e
     * @return $this
     */
    public function setException(Exception $e)
    {
        $this->exception = $e;
        return $this;
    }

    /**
     * 抛出异常
     *
     * @param Exception $e
     */
    public function throwException(Exception $e)
    {
        return $this->co->throw($e);
    }

    /**
     * 协程栈
     *
     * @param Generator $coroutine
     * @return Generator
     */
    protected function stackedCoroutine(Generator $coroutine) {
        $stack = new SplStack;
        $exception = null;

        for (;;) {
            try {
                if ($exception) {
                    $coroutine->throw($exception);
                    $exception = null;
                    continue;
                }

                $value = $coroutine->current();

                if ($value instanceof Generator) {
                    $stack->push($coroutine);
                    $coroutine = $value;
                    continue;
                }

                // 协程返回值代理
                $isReturnValue = $value instanceof Value;
                if (!$coroutine->valid() || $isReturnValue) {
                    if ($stack->isEmpty()) {
                        return;
                    }

                    $coroutine = $stack->pop();
                    // 发送协程返回值
                    $coroutine->send($isReturnValue ? $value->get() : null);
                    continue;
                }

                try {
                    // 获取协程发送值
                    $sendValue = (yield $coroutine->key() => $value);
                } catch (Exception $e) {
                    $coroutine->throw($e);
                    continue;
                }

                $coroutine->send($sendValue);
            } catch (Exception $e) {
                if ($stack->isEmpty()) {
                    throw $e;
                }

                $coroutine = $stack->pop();
                $exception = $e;
            }
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
            return $this->co->current();
        }
        if ($this->exception) {
            $result = $this->co->throw($this->exception);
            $this->exception = null;
            return $result;
        }

        $result = $this->co->send($this->value);
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
        return !$this->co->valid();
    }

}
