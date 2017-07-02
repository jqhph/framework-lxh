<?php
/**
 * 配置文件管理
 *
 * @author Jqh
 * @date   2017/5/16 13:53
 */

namespace Lxh\Config;

use Lxh\Helper\Entity;

class Config extends Entity
{
    protected $root = __ROOT__;// 项目目录

    protected $prefix = 'config';

    protected static $instance;

    /**
     * 在使用时才读取的配置文件路径
     *
     * @var string
     */
    protected $fileName;

    /**
     * 配置文件路径
     *
     * @var array
     */
    protected $confFiles = ['config'];

    public function __construct()
    {
        $this->fillConfig();
    }


    /**
     * 设置配置文件路径
     *
     * @param $file string
     * @return $this
     */
    public function file($file)
    {
        $this->fileName = $file;
        return $this;
    }

    /**
     * 获取配置信息
     *
     * @param string $name 多级参数用"."隔开, 如 get('db.mysql')
     * @param string|array|null $default 默认值
     */
    public function get($key = null, $default = null)
    {
        if ($this->fileName) {
            $tmp = explode(',', $key);
            if (! isset($this->attrs[$tmp[0]])) {
                $this->attrs += include "{$this->root}{$this->fileName}.php";
            }
            $this->fileName = null;
        }

        return parent::get($key, $default);
    }

    // 加载配置文件
    public function fillConfig()
    {
        $pre = "{$this->root}{$this->prefix}/";

        foreach ($this->confFiles as & $f) {
            $file = "{$pre}{$f}.php";

            $this->attrs += (array) include $file;
        }

        foreach ((array) $this->get('add-config') as & $filename) {
            $this->attrs += include "{$pre}{$filename}.php";
        }

        foreach ((array) $this->get('add-config-name') as $k => & $filename) {
            $this->attrs[basename($filename)] = include "{$pre}{$filename}.php";
        }
    }

    // 获取容器配置参数
    public function getContainerConfig()
    {
        return include $this->root . 'config/container/container.php';
    }
}
