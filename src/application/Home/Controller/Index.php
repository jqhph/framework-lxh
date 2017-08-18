<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Home\Controller;

class Index extends Controller
{
    public function actionList()
    {
        $cache = cache('home');

        $key = 'i' . currency()->current();

        if ($data = $cache->get($key)) {
            return $data;
        }

        $carousel = $this->getCarousel();

        $commends = $this->getRecommend();

        $prod = $this->getModel('Produce');

        // 热销
        $hot = $prod->hotList();

        // 新品
        $new = $prod->newList();

        // 所有分类
        $sortList = $this->menu->all();

        assign('newProds', $new);
        assign('hotProds', $hot);
        assign('allsort', $sortList);
        assign('reMenu', $commends['menu']);
        assign('reImgs', $commends['imgs']);
        assign('carousel', $carousel);

        $data = fetch_complete_view('List');

        // 缓存半小时
        $cache->set($key, $data, 1800);

        return $data;
    }

    // 获取轮播窗配置
    protected function getCarousel()
    {
        return [
            ['url' => '', 'src' => '//new.styleAny.com/static/images/1920-500.jpg'],
//            ['url' => '', 'src' => 'http://endata.bellelily.com/afficheimg/2017-07-31/20170731wtentt.jpg'],
        ];
    }

    // 首页推荐布局
    protected function getRecommend()
    {
        // 推荐菜单分类
        $menu = [
            ['href' => '', 'name' => 'WOMEN',],
            ['href' => '', 'name' => 'MEN',],
            ['href' => '', 'name' => 'CHILD',],
            ['href' => '', 'name' => 'Dresses',],
            ['href' => '', 'name' => 'Accessories',],
        ];

        // 推荐图片数组
        $imgs = [
            'aside' => [
                ['src' => '/images/280-160.jpg', 'href' => ''],
                ['src' =>  '/images/280-400.jpg', 'href' => ''],
            ],
            'left' => ['src' => '/images/300-500-woman.jpg', 'href' => ''],
            'right' => [
                ['src' => '/images/265-265-01.jpg', 'href' => ''],
                ['src' => '/images/265-265-02.jpg', 'href' => ''],
                ['src' => '/images/545-220.jpg', 'href' => ''],
            ],
        ];

        return ['menu' => & $menu, 'imgs' => & $imgs];
    }

}
