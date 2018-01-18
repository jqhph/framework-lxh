<?php

namespace Lxh\Plugins;

use Lxh\Contracts\PluginInstaller;
use Lxh\Contracts\PluginRegister;
use Lxh\Exceptions\InternalServerError;
use Lxh\Exceptions\InvalidArgumentException;

class Plugin
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var string
     */
    protected $assetsPath;

    /**
     * @var string
     */
    protected $installerPath;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var PluginRegister
     */
    protected $application;

    /**
     * @var string
     */
    protected $srcPath = '';

    public function __construct(Manager $manager, $plugin)
    {
        $this->manager = $manager;
        $this->name = $plugin;
        $this->setupPaths();
    }

    protected function setupPaths()
    {
        $this->path = __PLUGINS__ . $this->name;
        $this->configPath = "{$this->path}/config/app.php";
        $this->appPath = "{$this->path}/src/Application.php";
        $this->installerPath = "{$this->path}/src/Installer/Handler.php";
        $this->assetsPath = "{$this->path}/assets";
    }

    /**
     * @return string
     */
    public function getApplicationPath()
    {
        return $this->appPath;
    }

    /**
     * @return string
     */
    public function getInstallerPath()
    {
        return $this->installerPath;
    }

    /**
     * @return string
     */
    public function getSrcPath()
    {
        return "plugins/{$this->name}/src";
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 禁用插件
     *
     * @return mixed
     */
    public function disable()
    {
        return resolve('config')->delete("plugins.{$this->name}");
    }

    /**
     * 启用插件
     *
     * @return mixed
     */
    public function enable()
    {
        $plugins = config('plugins');
        $namespace = $this->getNamespace();
        $plugins[$this->name] = $namespace;

        return resolve('config')->save(['plugins' => &$plugins,]);
    }

    /**
     * 获取插件路径
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function getAssetsPath()
    {
        return $this->assetsPath;
    }

    /**
     * 检测插件是否存在
     *
     * @return bool
     */
    public function exist()
    {
        return is_dir($this->path);
    }

    /**
     * 获取插件下的类
     *
     * @param $name
     * @return string
     */
    protected function getClass($name)
    {
        $namespace = $this->getNamespace();

        return "$namespace\\$name";
    }

    /**
     * @return PluginRegister
     */
    public function getApplication()
    {
        if (! $this->application) {
            $class = $this->getClass('Application');
            if (!class_exists($class)) {
                include $this->appPath;
            }

            $this->application = new $class();
        }

        return $this->application;
    }


    /**
     * @param $namespace
     * @param $plugin
     * @return PluginRegister
     */
    public static function createApplication($namespace)
    {
        $class = "$namespace\\Application";

        $app = new $class();

        if (!$app instanceof PluginRegister) {
            throw new InvalidArgumentException("Invalid class Application");
        }
        return $app;
    }

    /**
     * 检查配置文件是否存在
     *
     * @return bool
     */
    public function configExist()
    {
        return is_file($this->configPath);
    }

    /**
     * 获取配置参数
     *
     * @param null $key
     * @param null $def
     * @return array|mixed
     */
    public function config($key = null, $def = null)
    {
        if (! $this->config) {
            $this->config = (array) include $this->configPath;
        }
        if ($key === null) {
            return $this->config;
        }

        return get_value($this->config, $key, $def);
    }

    /**
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->config('namespace');
    }

    /**
     * @return PluginInstaller
     */
    public function getInstaller()
    {
        if (! is_file($this->installerPath)) {
            return false;
        }
        include_once $this->installerPath;

        $class = "{$this->getNamespace()}\\Installer\\Handler";
        if (class_exists($class)) {
            $installer = new $class();
            if ($installer instanceof PluginInstaller) {
                return $installer;
            }
        }

        return false;
    }

    /**
     * 检查插件是否有效
     *
     * @param $plugin
     * @return void
     */
    public function valid()
    {
        // 检查插件路径是否存在
        if (! $this->exist()) {
            throw new InvalidArgumentException("The plugin[{$this->name}] not exist");
        }

        // 检查配置文件是否存在
        if (! $this->configExist()) {
            throw new InvalidArgumentException("The plugin's config file not found!");
        }

        // 检查配置文件是否有效
        if (! $this->getNamespace()) {
            throw new InvalidArgumentException("Can't not found the namespace param!");
        }

        // 检查注册类是否存在
        if (! is_file($this->appPath)) {
            throw new InvalidArgumentException("The file[{$this->appPath}] not exist!");
        }

        // 检查注册类是否有效
        $app = $this->getApplication();
        if (! $app instanceof PluginRegister) {
//            $class = get_class($app);
//            $interf = PluginRegister::class;
            throw new InvalidArgumentException("Invalid class Application");
        }
    }

    public function string()
    {
        $config = $this->config();

        return \Lxh\Helper\Util::arrayToText($config);
    }

}
