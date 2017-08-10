<?php
/**
 * 公共类
 *
 * @author Jqh
 * @date   2017/8/10 18:25
 */

namespace Lxh\Kernel\Spiders;

use Lxh\Kernel\Client;

abstract class Handler
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
        $this->client = http();
    }

    /**
     * @return Crawler
     */
    public function crawler()
    {
        return $this->crawler;
    }

}
