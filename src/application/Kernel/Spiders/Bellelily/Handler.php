<?php
/**
 * Bellelily网站爬虫
 *
 * @author Jqh
 * @date   2017/8/10 18:22
 */

namespace Lxh\Kernel\Spiders\Bellelily;

use Lxh\Kernel\Spiders\Handler as Basic;

class Handler extends Basic
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Product
     */
    protected $prod;

    protected $prototype = 'http';

    protected $host = 'www.bellelily.com';

    /**
     * 爬虫抓取并发数
     *
     * @var int
     */
    protected $concurrenceNum = 5;

    /**
     * 暂停时间（毫秒）
     *
     * @var int
     */
    protected $sleep = 10;

    // 获取url
    public function url($name = null)
    {
        $name = trim($name, '/');

        if (strpos($name, '://') !== false) {
            return $name;
        }

        return "{$this->prototype}://{$this->host}/$name";
    }

    public function getConcurrenceNum()
    {
        return $this->concurrenceNum;
    }

    /**
     * 抓取产品分类数据
     *
     * @return array
     */
    public function makeCategoriesData($useCache = true)
    {
        return $this->category()->fetch($this->concurrenceNum, $useCache);
    }

    /**
     * 抓取产品数据
     *
     * @return array
     */
    public function makeProdsData()
    {
        return $this->prod()->fetch($this->concurrenceNum);
    }

    /**
     * 抓取分类数据
     *
     * @return Category
     */
    protected function category()
    {
        return $this->category ? $this->category : ($this->category = new Category($this));
    }

    protected function prod()
    {
        return $this->prod ? $this->prod : ($this->prod = new Product($this));
    }
}
