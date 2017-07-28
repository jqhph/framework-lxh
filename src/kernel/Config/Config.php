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
     * 可写配置文件路径
     *
     * @var string
     */
    protected $writableConfigName = 'writable';

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

    // 加载配置文件
    public function fillConfig()
    {
        $pre = $this->getBasePath();

        foreach ($this->confFiles as & $f) {
            $file = "{$pre}{$f}.php";

            $this->attrs += (array)include $file;
        }

        foreach ((array)$this->get('add-config') as & $filename) {
            $this->attrs += include "{$pre}{$filename}.php";
        }

        foreach ((array)$this->get('add-config-name') as $k => & $filename) {
            $this->attrs[basename($filename)] = include "{$pre}{$filename}.php";
        }

        $this->attrs += include $this->getWritableConfigPath();
    }

    public function getBasePath()
    {
        return "{$this->root}{$this->prefix}/";
    }

    // 获取容器配置参数
    public function getContainerConfig()
    {
        return include $this->root . 'config/container/container.php';
    }

    /**
     * 获取环境配置文件路径
     *
     * @return string
     */
    protected function getEnvConfigPath($filename)
    {
        return $this->getBasePath() . __ENV__ . "/$filename.php";
    }

    protected function getWritableConfigPath()
    {
        return $this->getEnvConfigPath($this->writableConfigName);
    }

    /**
     * 保存配置
     *
     * @param  array $opts 要保存的配置数据
     * @return bool
     */
    public function save(array $opts)
    {
        return file_manager()->mergeContents($this->getWritableConfigPath(), $opts);
    }

}
