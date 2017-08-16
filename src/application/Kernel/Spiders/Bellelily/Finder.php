<?php

namespace Lxh\Kernel\Spiders\Bellelily;

use Lxh\Http\Client;
use Lxh\Kernel\Cache\Cache;
use Lxh\Kernel\Spiders\SimpleHtmlDomNode;
use \Lxh\File\FileManager;

abstract class Finder
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
    protected $retryTimes = 5;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var FileManager
     */
    protected $file;

    protected $parsers = [];

    protected $installers = [];

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;

        $this->file = file_manager();

        $this->cache = cache();
    }

    /**
     * 获取解析器
     *
     * @param string $name
     * @return self
     */
    protected function parser($name)
    {
        if (isset($this->parser[$name])) return $this->parser[$name];

        $class = "Lxh\\Kernel\\Spiders\\Bellelily\\Parsers\\$name";

        return $this->parser[$name] = new $class($this->handler, $this);
    }

    protected function installer($name)
    {
        if (isset($this->installers[$name])) return $this->installers[$name];

        $class = "Lxh\\Kernel\\Spiders\\Bellelily\\Install\\$name";

        return $this->installers[$name] = new $class($this->handler, $this);
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
    protected function setRecord(array $data, $id = null)
    {
        if ($id === null) {
            $id = $data['id'];
        }

        // 如果记录已经保存过，则跳过
        if (isset($this->records[$id])) {
            return;
        }

        $this->records[$data['id']] = & $data;
    }

    protected function setRecordItem($id, $key, $val)
    {
        if (! isset($this->records[$id])) {
            return;
        }

        $this->records[$id][$key] = & $val;
    }

    // 记录请求信息
    protected function saveRequestInfo($url, $useTime, $error, array $data = [])
    {
        return $this->handler->crawler()->saveRequestInfo($url, $useTime, $error, $data);
    }

    /**
     * 获取抓取的记录
     *
     * @return array
     */
    public function & records($id = null)
    {
        if ($id === null) return $this->records;

        return $this->records[$id];
    }

    /**
     * HTTP GET
     *
     * @return mixed
     */
    protected function requestGet($url, $retry = null, $isRetry = false)
    {
        $retry = $retry === null ? $this->retryTimes : $retry;

        $s = microtime(true);

        $client = $this->client();

        $content = $client->get($url)->then();

        $error = $client->response('error');

        if ($error) {
            $msg = "请求 [$url] 出错：{$error}！";
            if ($isRetry) {
                $msg = "请求 [$url] 出错（重试）：{$error}！剩余重试次数：$retry";
            }
            $this->warning($msg);
        }

        // 记录请求信息
        $this->saveRequestInfo($url, round(microtime(true) - $s, 4), $error);

        if ($error && $retry > 0) {
            return $this->requestGet($url, $retry - 1, true);
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
     * @param  bool $useCache 是否获取缓存中的数据
     * @return array
     */
    abstract public function fetch($c = 1, $useCache = true);

    /**
     * 弹出数组前面$num个元素
     *
     * @return array
     */
    public function arrayShift(array & $data, $num)
    {
        if (! $data) return [];

        $content = [];
        for ($i = 0; $i < $num; $i ++) {
            if (count($data) < 1) return $content;

            $content[] = array_shift($data);
        }

        return $content;
    }

}
