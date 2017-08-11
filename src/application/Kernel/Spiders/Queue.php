<?php
/**
 * 队列
 *
 * @author Jqh
 * @date   2017/8/11 16:06
 */

namespace Lxh\Kernel\Spiders;

use SplQueue;

class Queue
{
    protected $list;

    public function __construct()
    {
        $this->list = new SplQueue();
    }

    public function next()
    {
        return $this->list->next();
    }

    public function push($data)
    {
        return $this->list->push($data);
    }

    public function count()
    {
        return $this->list->count();
    }
}
