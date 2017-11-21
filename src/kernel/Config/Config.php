<?php
/**
 * 配置文件管理
 *
 * @author Jqh
 * @date   2017/5/16 13:53
 */

namespace Lxh\Config;

use Lxh\Exceptions\InvalidArgumentException;
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
    protected $confFiles = ['config', 'app'];

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

            if (is_file($file)) {
                $this->items += include $file;
            }
        }
        if (count($this->items) < 1) {
            throw new InvalidArgumentException('初始配置文件不存在或文件内容为空！');
        }

        foreach ((array)$this->get('add-config') as & $filename) {
            $this->items += include "{$pre}{$filename}.php";
        }

        foreach ((array)$this->get('add-config-name') as $k => & $filename) {
            $this->items[basename($filename)] = include "{$pre}{$filename}.php";
        }

        $this->writableData = include $this->getWritableConfigPath();

        $this->items += $this->writableData;
    }

    /**
     * 手动载入配置文件
     *
     * @param string $path
     * @return static
     */
    public function load($path)
    {
        if (! isset($this->loaded[$path])) {
            $file = $this->getBasePath() . $path;
            if (is_file($file)) {
                throw new InvalidArgumentException('The config file is not exist! [' . $path . ']');
            }
            $this->fill(include $file);
            $this->loaded[$path] = true;
        }

        return $this;
    }

    /**
     * 载入环境文件夹配置文件
     *
     * @param string $path
     * @return static
     */
    public function loadEnv($path)
    {
        $path = __ENV__ . '/' . $path;

        return $this->load($path);
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
