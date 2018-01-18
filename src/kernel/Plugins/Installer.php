<?php

namespace Lxh\Plugins;

use Lxh\Exceptions\InternalServerError;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Support\Composer;

class Installer
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * 已安装插件
     *
     * @var array
     */
    protected $installed = [];

    /**
     * 已卸载插件
     *
     * @var array
     */
    protected $uninstalled = [];

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var string
     */
    protected $composerJsonPath = '';

    /**
     * @var mixed
     */
    protected $output;

    /**
     * @var string
     */
    protected $assetsInstallPath;

    public function __construct(Manager $manager, $plugin)
    {
        $this->manager = $manager;
        $this->plugin = new Plugin($manager, $plugin);
        $this->composer = new Composer();
        $this->assetsInstallPath = resolve('app')->getPublicPath() . "assets/plugins";
    }

    /**
     * @param $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * 输出内容
     *
     * @param $msg
     */
    public function line($msg)
    {
        if ($this->output) {
            $this->output->line($msg);
        }
    }

    public function error($msg)
    {
        if ($this->output) {
            $this->output->error($msg);
        }
    }

    public function info($msg)
    {
        if ($this->output) {
            $this->output->info($msg);
        }
    }

    /**
     * 增加插件命名空间
     *
     * @param $name
     * @throws InvalidArgumentException
     */
    protected function addNamespace($name)
    {
        // 插件命名空间
        $namespace = $this->plugin->getNamespace();
        if ($this->composer->psr4NamespaceExist($namespace)) {
            // 已存在同样命名空间，插件已安装或存在同名插件
            throw new InvalidArgumentException("Namespace[{$namespace}] already exist!");
        }
        if (!$this->composer->addPsr4Namespace($namespace, $this->plugin->getSrcPath())) {
            throw new InvalidArgumentException("Add namespace[$namespace] failed!");
        }
        $this->composer->dumpOptimized();
        $this->info("Add namespace[$namespace] success!");
    }

    /**
     * 复制资源文件
     *
     * @param $name
     */
    protected function copyAssets($name)
    {
        $result = $this->recurseCopy($this->plugin->getAssetsPath(), "{$this->assetsInstallPath}/$name");
        if (! $result) {
            $this->error("Copy assets failed!");
        }
    }

    /**
     * 保存插件名称到配置文件
     *
     * @throws InvalidArgumentException
     */
    protected function saveConfig($name)
    {
        $plugins = config('plugins');
        $namespace = $this->plugin->getNamespace();
        $plugins[$name] = $namespace;

        if (!resolve('config')->save(['plugins' => &$plugins,])) {
            $this->composer->deletePsr4Namespace($namespace);
            throw new InvalidArgumentException("Save plugin in config failed!");
        }
//        $this->line("保存插件到配置文件成功");
    }

    /**
     * @return Plugin
     */
    public function plugin()
    {
        return $this->plugin;
    }

    /**
     * 要安装的插件
     *
     * @param string $plugin 插件名称
     */
    public function install()
    {
        // 插件名称
        $name = $this->plugin->getName();

        $this->line("====== Install plugin: {$name} ======");
        $this->line("Check plugin valid...");

        // 检测插件是否有效
        $this->plugin->valid();
        $this->info("The plugin is installable!");

        // 增加插件命名空间
        $this->addNamespace($name);

        // 复制资源文件
        $this->copyAssets($name);

        //保 存插件名称到配置文件
        $this->saveConfig($name);

        // 检测插件安装器
        if ($installer = $this->plugin->getInstaller()) {
            $installer->install($this->plugin);
        }

        $this->info("Installed!");
    }

    /**
     * @param $src
     * @param $dst
     */
    public function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        if (! is_dir($dst)) {
            files()->mkdir($dst, null, true);
        }

        $success = true;

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if (copy($src . '/' . $file, $dst . '/' . $file)) {
                        $this->info('Copy "' . $src . '/' . $file . '" to "' .$dst . '/' . $file . '" success!');
                    } else {
                        $this->error('Copy "' . $src . '/' . $file . '" to "' .$dst . '/' . $file . '" failed!');
                        $success = false;
                    }
                }
            }
        }
        closedir($dir);

        return $success;
    }

    public function uninstall()
    {
        $name = $this->plugin->getName();
        $namespace = $this->plugin->getNamespace();

        files()->removeInDir($this->assetsInstallPath . '/' . $name, true);
        $this->line("Remove assets success!");

        $this->composer->deletePsr4Namespace($namespace);
        $this->composer->dumpOptimized();
        $this->line("Remove namespace success!");


        if (!resolve('config')->delete("plugins.$name")) {
            throw new InvalidArgumentException("Uninstall plugin fialed!");
        }

        // 检测插件安装器
        if ($installer = $this->plugin->getInstaller()) {
            $installer->uninstall($this->plugin);
        }

        $this->info("Uninstalled!");
    }


}
