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
    /**
     * 项目目录
     *
     * @var string
     */
    protected $root = __ROOT__;

    /**
     * @var string
     */
    protected $prefix = 'config';

    /**
     * @var string
     */
    protected $cachePath = 'resource/config/';

    /**
     * @var string
     */
    protected $env = __ENV__;

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

    /**
     * @var bool
     */
    protected $useCache = true;

    public function __construct()
    {
        $this->useCache = defined('USE_CONFIG_CACHE') ? USE_CONFIG_CACHE : true;

        $this->attachItems();
    }

    /**
     * 重新加载配置文件
     *
     * @return static
     */
    public function refetch()
    {
        $this->items = [];

        $this->attachItems(true);

        return $this;
    }

    /**
     * 获取缓存文件夹路径
     *
     * @return string
     */
    public function getCachePath()
    {
        return __ROOT__ . $this->cachePath . $this->env . '.php';
    }

    /**
     * 读取缓存数据
     *
     * @return boolean
     */
    protected function readCache()
    {
        if (!$this->useCache) {
            return false;
        }
        // 如果存在缓存，则直接加载缓存中的文件
        if (! is_file($cachePath = $this->getCachePath())) {
            return false;
        }
        $cache = include $cachePath;
        if (!is_array($cache) || count($cache) < 1) {
            return false;
        }
        $this->items = &$cache;
        return true;
    }

    /**
     * 设置环境
     *
     * @param string $env
     * @return static
     */
    public function env($env)
    {
        if (! in_array($env, [ENV_PROD, ENV_TEST, ENV_DEV])) {
            return $this;
        }
        $this->env = $env;

        return $this;
    }

    /**
     * 加载配置文件
     *
     * @param bool $useCurrentEnv 是否加载指定环境配置文件
     * @return void
     */
    public function attachItems($useCurrentEnv = false)
    {
        if ($this->readCache()) {
            return;
        }

        $pre = $this->getBasePath();

        foreach ($this->confFiles as &$f) {
            $file = "{$pre}{$f}.php";

            if (is_file($file)) {
                $this->items = array_merge($this->items, (array) include $file);
            }
        }
        if (count($this->items) < 1) {
            throw new InvalidArgumentException("Config file[$file] not found!");
        }

        $this->attachFiles($this->get('add-config'), $useCurrentEnv);

        $this->writableData = include $this->getWritableConfigPath();
        $this->items = array_merge($this->items, (array) $this->writableData);

        $this->saveCache();
    }

    protected function attachFiles($files, $useCurrentEnv = false)
    {
        $pre = $this->getBasePath();

        if ($useCurrentEnv) {
            $envpre = '/' . __ENV__ . '/';
            $currentEnvpre = '/' . $this->env . '/';
        }

        foreach ((array) $files as $k => &$filename) {
            $path = "{$pre}{$filename}.php";
            if (isset($envpre)) {
                $path = str_replace($envpre, $currentEnvpre, $path);
            }
            if (! is_file($path)) {
                throw new InvalidArgumentException("Config file[$path] not found!");
            }

            if (is_string($k)) {
                $this->items[$k] = include $path;
            } else {
                $this->items = array_merge($this->items, (array) include $path);
            }
        }
    }

    // 保存缓存
    public function saveCache()
    {
        if (! $this->useCache) return;

        files()->putPhpContents($this->getCachePath(), $this->items);
    }

    // 清除缓存
    public function removeCache()
    {
        if (! $this->useCache) return;

        return files()->remove($this->getCachePath());
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
        $path = $this->env . '/' . $path;

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
        return $this->getBasePath() . $this->env . "/$filename.php";
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
        $res = files()->mergeContents($this->getWritableConfigPath(), $opts);

        $this->removeCache();
        $this->refetch();
        $this->saveCache();

        return $res;
    }

}
