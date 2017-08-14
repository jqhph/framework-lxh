<?php

namespace Lxh\Kernel\Spiders\Bellelily\Parsers;

use Lxh\Kernel\Spiders\Bellelily\Finder;
use Lxh\Kernel\Spiders\Bellelily\Handler;

class Parser
{
    protected $handler;

    protected $finder;

    protected $cache;

    public function __construct(Handler $handler, Finder $finder)
    {
        $this->handler = $handler;
        $this->finder = $finder;
        $this->cache = cache();
    }

    public function handler(& $html)
    {
        
    }

    public function dom(& $html)
    {
        return $this->handler->crawler()->dom($html);
    }

    /**
     * 解析分类界面seo信息
     *
     * @param string|object $html
     * @return array
     */
    protected function parseSEO(& $dom)
    {
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
}
