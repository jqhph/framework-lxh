<?php
/**
 * 抓取分类数据
 *
 * @author Jqh
 * @date   2017/8/10 19:25
 */
namespace Lxh\Kernel\Spiders\Bellelily;

class Category extends Basic
{
    protected $cateUrl = '{name}-t-{id}';

    protected $notTopCates = [
        'sale', 'tops', 'collections', 'new'
    ];

    /**
     * 手动配置的顶级分类
     *
     * @var array
     */
    protected $tops = [
        ['id' => '610', 'name' => 'Men', 'url' => '/men-t-610']
    ];

    /**
     * 记录并发抓取时出错的url，并发抓取结束后重试
     *
     * @var array
     */
    protected $errorUrl = [];

    /**
     * 获取分类界面url
     *
     * @param  int $id 分类id
     * @param  string $name 分类名称
     * @return string
     */
    protected function cateUrl($id, $name = 'test')
    {
        return $this->handler->url(
            strtr($this->cateUrl, [
                '{name}' => & $name,
                '{id}' => & $id
            ])
        );
    }

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
            // 请求失败， 重试
            $this->error("抓取顶级分类失败！[$url]请求失败，重试次数：{$this->retryTimes}");

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
     * @return array
     */
    public function fetch($c = 1)
    {
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

        return $records;
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
        $selector = '.dirWrap .dirLeft .top_dd dd';

        $client = $this->client();

        $i = 1;
        while ($data = $this->arrayShift($tops, $c)) {

            foreach ($data as $r) {
                $client->get($this->handler->url($r['url']));
                if (empty($r['id'])) {
                    // 没有id，则是重试数据
                    $this->info("失败重试 [{$r['url']}]，剩余重试次数：{$retryTimes}");
                }

            }

            $client->then(function ($output, $info, $error, $request) use ($i, $selector, $retryTimes) {
                if ($error) {
                    if ($retryTimes > 0) {
                        $this->fetchSubCates([['url' => & $request['url'],]], $retryTimes - 1);
                    }
                    return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
                }

                $dom = $this->dom($output);

                $parentId = $this->getIdFormUrl($request['url']);

                foreach ($dom->find($selector) as & $dd) {
                    $a = $dd->find('a', 0);

                    $href = $a->href;

                    $id = $this->getIdFormUrl($href);

                    // 保存二级分类
                    $this->setRecord([
                        'id' => $id,
                        'name' => $a->innertext,
                        'parent_id' => $parentId,
                    ]);

                    // 三级分类
                    foreach ((array) $dd->find('dd') as & $thridDd) {
                        $a = $thridDd->find('a', 0);

                        $this->setRecord([
                            'id' => $this->getIdFormUrl($a->href),
                            'name' => $a->innertext,
                            'parent_id' => $id,
                        ]);

                    }
                }

            });

            $i ++;
        }
    }
}
