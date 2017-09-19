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

    /**
     * 可写配置参数
     *
     * @var array
     */
    protected $writableData = [];

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

            $this->items += (array) include $file;
        }

        foreach ((array)$this->get('add-config') as & $filename) {
            $this->items += (array) include "{$pre}{$filename}.php";
        }

        foreach ((array)$this->get('add-config-name') as $k => & $filename) {
            $this->items[basename($filename)] = (array) include "{$pre}{$filename}.php";
        }

        $this->writableData = include $this->getWritableConfigPath();

        $this->items += $this->writableData;
    }

    /**
     * 获取可变动数据
     *
     * @return array
     */
    public function getVariableData()
    {
        return $this->writableData;
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
        return files()->mergeContents($this->getWritableConfigPath(), $opts);
    }

}
