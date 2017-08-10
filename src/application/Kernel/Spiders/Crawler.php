<?php
/**
 * 爬虫
 *
 * @author Jqh
 * @date   2017/8/10 15:37
 */

namespace Lxh\Kernel\Spiders;

use Lxh\Exceptions\Error;

class Crawler
{
    /**
     * @var string
     */
    protected $siteName = 'Bellelily';

    protected $drivers = [];

    public function __construct()
    {
        // 无限时间
        set_time_limit(0);
    }

    /**
     * @return SimpleHtmlDomNode
     */
    public function dom(& $html)
    {
        return new SimpleHtmlDom($html);
    }

    /**
     * 抓取产品分类数据
     *
     * @return array
     */
    public function makeClassifiedData()
    {
        return $this->driver()->makeClassifiedData();
    }

    /**
     *
     * @param  string $name
     * @return Handler
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->siteName;
        
        if (isset($this->drivers[$name])) return $this->drivers[$name];

        $class = "Lxh\\Kernel\\Spiders\\{$name}\\Handler";

        return $this->drivers[$name] = new $class($this);
    }

    /**
     * 成行输出结果
     *
     * @param
     * @return void
     */
    public function info($data)
    {
        // 记录爬虫日志
        logger('Crawler')->info($data);

        return $this->output($data);
    }

    public function error($data)
    {
        // 记录爬虫日志
        logger('Crawler')->error($data);

        return $this->output($data, 'error');
    }

    public function success($data)
    {
        // 记录爬虫日志
        logger('Crawler')->info("[SUCCESS] $data");

        return $this->output($data, 'success');
    }

    public function warning($data)
    {
        // 记录爬虫日志
        logger('Crawler')->warning($data);

        return $this->output($data, 'warning');
    }

    /**
     * 输出数据
     *
     * @return void
     */
    public function output($data, $type = 'info')
    {
        // 判断是否是命令行
        if (make('http.request')->isCli()) {
            $n = "\n";
        } else {
            $n = '<hr>';
        }

        if (is_array($data)) $data = json_encode($data);

        $type = strtoupper($type);

        echo "[$type] $data{$n}";
    }

}
