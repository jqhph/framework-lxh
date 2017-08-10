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
        'new', 'sale', 'tops'
    ];

    /**
     * 所有分类结果
     *
     * @var array
     */
    protected $result = [];

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

            $tmp = explode('-', $a->href);

            $sorts[] = [
                'id' => end($tmp),
                'url' => $a->href,
                'name' => $a->innertext,
            ];
        }

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
     * 抓取数据
     *
     * @param  int $c 并发抓取数
     * @return array
     */
    public function fetch($c = 1) {
        $tops = $this->fetchTopCate();

        if (! $tops) {
            return false;
        }

        $this->info("开始抓取子分类数据，并发抓取数：$c ...");

        // 进入分类页面抓取分类信息，如 http://www.bellelily.com/clothing-t-444
        $selector = '.dirWrap .dirLeft .top_dd dd';

        $i = 1;
        while ($data = $this->arrayShift($tops, $c)) {
            $client = $this->client();

            foreach ($data as & $r) {
                $client->get($this->handler->url($r['url']));
                
            }

            $client->then(function ($output, $info, $error, $request) use ($i) {
                if ($error) {
                    return $this->warning("抓取 [{$request['url']}] 数据出错，错误信息：$error");
                }

                debug($info);
            });

            $i ++;
        }


//        return $this;
    }
}
