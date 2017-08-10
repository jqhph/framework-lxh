<?php

namespace Lxh\Kernel\Spiders\Bellelily;

use Lxh\Http\Client;
use Lxh\Kernel\Spiders\SimpleHtmlDomNode;

abstract class Basic
{
    /**
     * @var Handler
     */
    protected $handler;

    /**
     * 抓取记录
     *
     * @var array
     */
    private $records = [];

    /**
     * 重试时间
     *
     * @var int
     */
    protected $retryTimes = 3;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     *
     * @return Client
     */
    protected function client()
    {
        return http();
    }

    /**
     * @return SimpleHtmlDomNode
     */
    public function dom($content)
    {
        return $this->handler->crawler()->dom($content);
    }

    /**
     * 保存抓取记录
     *
     */
    protected function setRecord(array $data)
    {
        // 如果记录已经保存过，则跳过
        if (isset($this->records[$data['id']])) {
            return;
        }

        $this->records[$data['id']] = & $data;
    }

    /**
     * 获取抓取的记录
     *
     * @return array
     */
    public function & records()
    {
        return $this->records;
    }

    /**
     * HTTP GET
     *
     * @return mixed
     */
    protected function requestGet($url, $retry = null)
    {
        $retry = $retry === null ? $this->retryTimes : $retry;

        $content = $this->client()->get($url)->then();

        if (! $content && $retry > 0) {
            return $this->requestGet($url, $retry - 1);
        }
        return $content;
    }


    /**
     * 输出数据 info
     *
     * @param  string | array $data
     * @return void
     */
    protected function info($data)
    {
        return $this->handler->crawler()->info($data);
    }

    /**
     * 输出数据 error
     *
     * @param  string | array $data
     * @return void
     */
    public function error($data)
    {
        return $this->handler->crawler()->error($data);
    }

    /**
     * 输出数据 success
     *
     * @param  string | array $data
     * @return void
     */
    public function success($data)
    {
        return $this->handler->crawler()->success($data);
    }

    public function warning($data)
    {
        return $this->handler->crawler()->warning($data);
    }

    /**
     * 抓取数据
     *
     * @param  int $c 并发抓取数
     * @return array
     */
    abstract public function fetch($c = 1);

    /**
     * 弹出数组前面$num个元素
     *
     * @return array
     */
    protected function arrayShift(array & $data, $num)
    {
        if (! $data) return [];

        $content = [];
        for ($i = 0; $i < $num; $i ++) {
            if (count($data) < 1) break;

            $content[] = array_shift($data);
        }

        return $content;
    }

}
