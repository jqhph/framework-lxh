<?php
/**
 * 抓取分类数据
 *
 * @author Jqh
 * @date   2017/8/10 19:25
 */
namespace Lxh\Kernel\Spiders\Bellelily;

class Category extends Finder
{
    protected $notTopCates = [
        'sale', 'tops', 'collections', 'new'
    ];

    /**
     * 手动配置的顶级分类
     *
     * @var array
     */
    protected $tops = [
        ['id' => '610', 'name' => 'Men', 'url' => '/men-t-610', 'parent_id' => 0]
    ];

    /**
     * 缓存时间，秒
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * 记录并发抓取时出错的url，并发抓取结束后重试
     *
     * @var array
     */
    protected $errorUrl = [];

    protected $cacheKey = 'belle-categories';

    /**
     * 抓取顶级分类数据
     *
     * @return array
     */
    protected function fetchTopCate()
    {
        $this->info("\n开始抓取顶级分类数据...");

        $s = microtime(true);

        // 首页
        $url = $this->handler->url();

        $content = $this->requestGet($url);

        if (! $content) {
            // 重试之后最终请求失败
            $this->error("抓取顶级分类失败！[$url]请求失败，重试次数：{$this->retryTimes}，请检查网站是否能正常访问");

            return false;
        }

        $dom = $this->dom($content);

        $sorts = [];

        // 抓取首页导航栏
        $selector = '.navbar-inner ul li';

        foreach ($dom->find($selector) as & $li) {
            $a = $li->find('a', 0);

            if (in_array(strtolower($a->innertext), $this->notTopCates)) continue;

            $url = $a->href;

            $content = [
                'id' => $this->getIdFormUrl($url),
                'url' => $url,
                'name' => $a->innertext,
                'parent_id' => 0,
//                'seo' => $this->fetchSeoInfo($url)
            ];

            $sorts[] = $content;

            // 保存记录
            $this->setRecord($content);
        }

        // 合并手动配置的顶级分类
        foreach ($this->tops as & $t) {
            $this->setRecord($t);
        }
        $sorts = array_merge($sorts, $this->tops);

        $count = count($sorts);

        if (! $count) {
            $this->error('抓取顶级数据数量为0，请检查网站是否变布局！');
            return false;
        }

        $useTime = round(microtime(true) - $s, 4);

        $this->success("抓取顶级分类结束，共有{$count}条数据，耗时：$useTime\n");

        return $sorts;
    }

    /**
     * 进入分类界面抓取SEO信息
     *
     * @param  string $url
     * @return array
     */
    protected function fetchSeoInfo($url)
    {
        $content = $this->requestGet($url);

        if (! $content)
            return $this->info("抓取SEO信息失败 [$url]!");

        return $this->parseCatePageToSeo($content);
    }

    /**
     * 解析分类界面seo信息
     *
     * @param string|object $html
     * @return array
     */
    protected function parseCatePageToSeo(& $dom)
    {
        return [];
        if (is_string($dom)) {
            $dom = $this->dom($dom);
        }

        $head = $dom->find('head', 0);

        $title = $head->find('title', 0)->innertext;

        $desc = $head->find('meta[name="Description"]', 0)->content;

        $keyword = $head->find('meta[name="Keywords"]', 0)->content;

        return [
            'title' => & $title,
            'desc' => & $desc,
            'keyword' => & $keyword
        ];
    }


    // 从url中获取id
    protected function getIdFormUrl($url)
    {
        $tmp = explode('?', $url);
        $tmp = explode('-', $tmp[0]);
        return end($tmp);
    }

    /**
     * 抓取数据
     *
     * @param  int $c 并发抓取数
     * @param  bool $useCache 是否获取缓存中的数据
     * @return array
     */
    public function fetch($c = 1, $useCache = true)
    {
        return $this->install();

        if ($useCache && ($records = $this->cache->get($this->cacheKey))) {
            $count = count($records);
            $this->info("获取分类数据成功，缓存中存在数据，总数：{$count}！");
            return $records;
        }

        $tops = $this->fetchTopCate();

        if (! $tops) {
            return false;
        }

        $this->info("开始抓取子分类数据，并发抓取数：$c ...");

        $s = microtime(true);

        $this->fetchSubCates($tops, $c);

        $useTime = round(microtime(true) - $s, 4);

        $records = $this->records();

        $total = count($records);

        $this->success("抓取子分类数据结束，共抓取{$total}条数据，耗时：$useTime");

        // 缓存12小时
        $this->cache->set($this->cacheKey, $records);

        // 安装数据
        $this->install($records);

        return $records;
    }

    protected function install(array & $data = [])
    {
        $this->info('开始安装分类数据 ...');

        $s = microtime(true);

        if (! $data) {
            $data = $this->cache->get($this->cacheKey);

        }

        $total = count($data);

        $count = $this->installer('Category')->handler($data);

        $u = round(microtime(true) - $s, 4);

        $this->success("安装结束，共有{$total}条数据，耗时：{$u}");
    }

    /**
     * 抓取子分类
     *
     * @param array $tops
     * @param $c
     * @param $retryTimes int 失败重试次数
     */
    protected function fetchSubCates(array $tops, $c = 1, $retryTimes = 5)
    {
        // 进入分类页面抓取分类信息，如 http://www.bellelily.com/clothing-t-444
        $client = $this->client();

        while ($data = $this->arrayShift($tops, $c)) {

            $s = microtime(true);

            foreach ($data as $r) {
                $url = $this->handler->url($r['url']);

                if (empty($r['id'])) {
                    // 没有id，则是重试数据
                    $this->info("失败重试 [{$url}]，剩余重试次数：{$retryTimes}");
                }

                // 设置批量抓取url
                $client->get($url);
            }

            // 开始抓取
            $client->then(function ($output, $info, $error, $request) use ($c, $retryTimes, $s) {
                $useTime = round(microtime(true) - $s, 4);

                // 记录请求结果
                $this->saveRequestInfo($request['url'], $useTime, $error);

                if ($error) {
                    if ($retryTimes > 0) {
                        $this->fetchSubCates([['url' => & $request['url'],]], $c, $retryTimes - 1);
                    } else {
                        return $this->warning("抓取 [{$request['url']}] 数据失败（剩余重试次数0），错误信息：$error");
                    }

                    return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
                }

                // 解析抓取的数据结果
                $this->parseSubCatesHtml($request['url'], $output);
            });
        }
    }

    // 解析分类界面html
    protected function parseSubCatesHtml($url, & $output)
    {
        $selector = '.dirWrap .dirLeft .top_dd dd';

        $dom = $this->dom($output);

        // 获取顶级分类的seo信息
        $seo = $this->parseCatePageToSeo($dom);

        $parentId = $this->getIdFormUrl($url);

        // 保存seo信息
        $this->setRecordItem($parentId, 'SEO', $seo);

        foreach ($dom->find($selector) as & $dd) {
            $a = $dd->find('a', 0);

            $href = $a->href;

            $id = $this->getIdFormUrl($href);

            // 保存二级分类
            $this->setRecord([
                'id' => $id,
                'name' => $a->innertext,
                'parent_id' => $parentId,
                'SEO' => $this->fetchSeoInfo($this->handler->url($href))
            ]);

            // 三级分类
            foreach ((array) $dd->find('dd') as & $thridDd) {

                $a = $thridDd->find('a', 0);

                $parentHref = $thridDd->parent()->prev_sibling()->find('a', 0)->href;

                $this->setRecord([
                    'id' => $this->getIdFormUrl($a->href),
                    'name' => $a->innertext,
                    'parent_id' => $this->getIdFormUrl($parentHref),
                    'SEO' => $this->fetchSeoInfo($this->handler->url($a->href))
                ]);

            }
        }
    }
}
