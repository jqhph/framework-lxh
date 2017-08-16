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
    protected $timeout = 0;

    /**
     * 从接口获取的产品数据
     *
     * @var array
     */
    protected $prodApiInfo = [];

    /**
     * 已采集详情页缓存文件名数组
     *
     * @var array
     */
    protected $detailFileList = [];

    protected $detailHtmlDir = 'prods-detail-html';

    public function __construct(Handler $handler)
    {
        parent::__construct($handler);

        $this->detailFileList = $this->getDetailHtmlFileList();
    }

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

    // 安装数据
    protected function install(array & $data = [])
    {
        $this->info('开始安装产品数据 ...');

        $s = microtime(true);

        if (! $data) {
            $flist = $this->file->getFileList($this->cache->getTypePath('prods'));

            $data = [];

            $this->cache->setType('prods');

            foreach ($flist as & $id) {
                if ($prod = $this->cache->get($id)) {
                    $data[] = $prod;
                }
            }
        }

        $total = count($data);
        
        $count = $this->installer('Product')->handler($data);

        $u = round(microtime(true) - $s, 4);

        $this->success("安装结束，共有{$total}条数据，耗时：{$u}");
    }

    public function fetch($c = 1, $useCache = true)
    {

        return $this->install();

        // 抓取有效的产品id
        $ids = $this->fetchValidProdIds($c);

        $this->info("开始抓取产品详情数据 ...");

        $s = microtime(true);

        $total = 0;

        // 每次抓取3000个产品
        $i = 1;
        while ($data = $this->arrayShift($ids, 1000)) {
            $count = $this->fetchDetail($data, $c);

            $total += $count;

            // 暂停1000微妙
            usleep(1000);
            $i ++;
        }

        $useTime = round(microtime(true) - $s, 4);

        $this->success("抓取产品详情数据结束，总数：{$total}，耗时：{$useTime}");

        // 对已抓取数据进行解析
        $prods = $this->parseDetailHtml();

        // 安装数据
        $this->install($prods);

        return $ids;
    }

    protected function getDetailHtmlFileList()
    {
        return $this->file->getFileList($this->cache->getBasePath() . $this->detailHtmlDir);
    }

    /**
     * 解析已抓取的产品详情页数据，并缓存结果
     *
     * @return array
     */
    public function parseDetailHtml()
    {
        $this->info('开始解析详情页html数据 ... ');

        $s = microtime(true);

        $prods = [];

        foreach ($this->getDetailHtmlFileList() as $i => & $id) {
            if (! $html = $this->cache->setType($this->detailHtmlDir)->get($id)) {
                continue;
            }
            $prod = $this->parser('Product');

            // 设置id
            $prod->id = $id;

            $data = $prod->handler($html);

            $this->cache->setType('prods')->set($id, $data);

            $prods[] = $data;
        }

        $u = round(microtime(true) - $s, 4);

        $count = count($prods);

        $this->success("解析详情页数据结束，共解析{$count}个界面，耗时：{$u}");

        return $prods;
    }

    // 抓取产品详情页数据，并缓存
    protected function fetchDetail(array & $ids, $c, $retryTimes = 5)
    {
        $client = $this->client();

        $i = 0;
        // 批量取出数据
        while ($data = $this->arrayShift($ids, $c)) {

            $s = microtime(true);

            foreach ($data as & $id) {
                // 如果已经采集过的，则跳过
                if (in_array($id, $this->detailFileList)) continue;

                // 批量采集
                $client->get($this->detailUrl($id));
            }

            // 开始抓取数据
            $client->then(function ($output, $info, $error, $request) use ($s, $i, $c, $retryTimes) {
                $useTime = round(microtime(true) - $s, 4);
                // 从接口url中获取产品id
                $id = $this->getIdFormUrl($request['url']);

                // 记录请求结果
                $this->saveRequestInfo($request['url'], $useTime, $error);

                if ($error) {
                    if ($retryTimes > 0) {
                        // 重试
                        $t = [$id];
                        $this->fetchDetail($t, $c, $retryTimes - 1);
                    } else {
                        return $this->warning("抓取 [{$request['url']}] 数据失败（剩余重试次数0），错误信息：$error");
                    }

                    return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
                }

                // 请求成功
                $this->cache->setType($this->detailHtmlDir);

                // 缓存
                $this->cache->set($id, $output);

                $i++;
            });
        }

        return $i;
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
        $this->cache->setType()->set($key, $prodIds, $this->timeout);

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

                // 缓存api数据
                $this->cache->setType('prod-api-result')->set($id, $output);

                // 保存接口数据
                $this->prodApiInfo[$id] = 1;
            });
        }

    }

    protected function getMinId()
    {
        return 1;
        $id = $this->cache->get('valid-prod-min');
        return $id ?: 1;
    }
}