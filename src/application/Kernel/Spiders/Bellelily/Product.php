<?php
/**
 * 抓取产品数据爬虫
 *
 * @author Jqh
 * @date   2017/8/11 09:42
 */

namespace Lxh\Kernel\Spiders\Bellelily;

class Product extends Finder
{
    protected $prodUrlMode = '/{name}-g-{id}';

    protected $prodApiMode = '/goods/index/ajaxrealtime/goods_id/{id}';

    // 随时更新最大ID
    protected $maxId = 35000;

    /**
     * 缓存时间，秒
     *
     * @var int
     */
    protected $timeout = 1800;

    /**
     * 从接口获取的产品数据
     *
     * @var array
     */
    protected $prodApiInfo = [];

    protected function api($id)
    {
        return $this->handler->url(
            strtr($this->prodApiMode, ['{id}' => & $id])
        );
    }

    // 从url中获取id
    protected function getIdFormUrl($url, $symbol = '-')
    {
        $tmp = explode('?', $url);
        $tmp = explode($symbol, $tmp[0]);
        return end($tmp);
    }

    /**
     * 获取产品详情界面url
     *
     * @param  int $id 分类id
     * @param  string $name 分类名称
     * @return string
     */
    protected function detailUrl($id, $name = 'test')
    {
        return $this->handler->url(
            strtr($this->prodUrlMode, [
                '{name}' => & $name,
                '{id}' => & $id
            ])
        );
    }

    public function fetch($c = 1, $useCache = true)
    {
        $prods = [];

        // 抓取有效的产品id
        $ids = $this->fetchValidProdIds($c);

        $this->info("开始抓取产品详情数据 ...");

        $s = microtime(true);

        $total = 0;

        // 每次抓取3000个产品
        $i = 1;
        while ($data = $this->arrayShift($ids, 1000)) {
            $records = $this->fetchDetail($data, $c);

            $total += count($records);

            // 缓存数据
            $this->cache->set('prod-detail-' . $i, $records);

            // 暂停1000微妙
            usleep(1000);
            $i ++;
        }

        $useTime = round(microtime(true) - $s, 4);

        $this->success("抓取产品详情数据结束，总数：{$total}，耗时：{$useTime}");

        return $ids;
    }

    // 抓取产品详情页数据
    protected function fetchDetail(array & $ids, $c, $retryTimes = 5)
    {
        $client = $this->client();

        $records = [];

        while ($data = $this->arrayShift($ids, $c)) {

            $s = microtime(true);

            foreach ($data as & $id) {
                $url = $this->detailUrl($id);

                $client->get($url);
            }

            $client->then(function ($output, $info, $error, $request) use ($s, $records, $c, $retryTimes) {
                $useTime = round(microtime(true) - $s, 4);
                // 从接口url中获取产品id
                $id = $this->getIdFormUrl($request['url'], '/');

                // 记录请求结果
                $this->saveRequestInfo($request['url'], $useTime, $error);

                if ($error) {
                    if ($retryTimes > 0) {
                        $t = [$id];
                        $this->fetchDetail($t, $c, $retryTimes - 1);
                    } else {
                        return $this->warning("抓取 [{$request['url']}] 数据失败（剩余重试次数0），错误信息：$error");
                    }

                    return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
                }
            });
        }

        return $records;
    }

    // 抓取有效的产品id
    protected function fetchValidProdIds($c)
    {
        $key = 'valid-prod-ids';

        $min = $this->getMinId();

        $this->info("\n开始抓取有效的产品ID，ID区间 [{$min}-{$this->maxId}] ...");

        if ($prodIds = $this->cache->get($key)) {
            $count = count($prodIds);
            $this->info("获取产品ID数据成功，缓存中有数据，总数：{$count}");
            return $prodIds;
        }

        $ids = range($min, $this->maxId);

        $s = microtime(true);

        // 获取有效的产品数据
        $this->getNormalizeProdIds($ids, $c, $this->retryTimes);

        $prodIds = array_keys($this->prodApiInfo);

        $useTime = round(microtime(true) - $s, 4);

        $count = count($prodIds);

        $this->success("抓取有效产品ID结束，总数：{$count}，耗时：{$useTime}。");

        // 缓存抓取的数据
        $this->cache->set($key, $prodIds, $this->timeout);

        // 保存最大id为最小id
        $this->saveMinId($this->maxId);

        return $prodIds;
    }

    // 保存最小id
    protected function saveMinId($id)
    {
        return $this->cache->set('valid-prod-min', $id + 1);
    }

    // 获取有效的产品数据id数组
    protected function getNormalizeProdIds(array $ids, $c, $retryTimes = 5)
    {
        $client = $this->client();

        $s = microtime(true);

        while ($data = $this->arrayShift($ids, $c)) {
            $s = microtime(true);

            foreach ($data as & $r) {
                $client->get($this->api($r));
            }
        }

        $client->then(function ($output, $info, $error, $request) use ($s, $c, $retryTimes) {
            $useTime = round(microtime(true) - $s, 4);
            // 从接口url中获取产品id
            $id = $this->getIdFormUrl($request['url'], '/');

            // 记录请求结果
            $this->saveRequestInfo($request['url'], $useTime, $error);

            if ($error) {
                if ($retryTimes > 0) {
                    $this->getNormalizeProdIds([$id], $c, $retryTimes - 1);
                } else {
                    return $this->warning("抓取 [{$request['url']}] 数据失败（剩余重试次数0），错误信息：$error");
                }

                return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
            }

            // 接口返回false说明没有数据，直接跳过
            if ($output == 'false') return;

            $output = json_decode($output, true);

            // 保存接口数据
            $this->prodApiInfo[$id] = & $output;
        });
    }

    protected function getMinId()
    {
        $id = $this->cache->get('valid-prod-min');
        return $id ?: 1;
    }
}