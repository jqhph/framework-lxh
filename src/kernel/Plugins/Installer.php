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
     * 判断插件是否已安装
     *
     * @return bool
     */
    public function isInstalled()
    {
        $namespace = ucfirst(camel__case($this->plugin->getName(), '-'));

        if ($this->composer->psr4NamespaceExist($namespace)) {
            return true;
        }
        return false;
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

    public function warn($msg)
    {
        if ($this->output) {
            $this->output->warn($msg);
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
    public function copyAssets()
    {
        $result = $this->recurseCopy($this->plugin->getAssetsPath(), "{$this->assetsInstallPath}/{$this->plugin->getName()}");
        if (! $result) {
            $this->error("Copy assets failed!");
        }
    }

    /**
     * 从webserver目录复制插件回自身目录
     *
     * @param $name
     */
    public function copyAssetsInvert()
    {
        $result = $this->recurseCopy("{$this->assetsInstallPath}/{$this->plugin->getName()}", $this->plugin->getAssetsPath());
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
        if (!$this->plugin()->enable()) {
            $this->composer->deletePsr4Namespace($this->plugin()->getNamespace());
            throw new InvalidArgumentException("Save plugin in config failed!");
        }
//        $this->line("保存插件到配置文件成功");
    }

    /**
     * 安装composer依赖包
     */
    protected function requireComposerDependences()
    {
        if (!$dependences = $this->plugin->getComposerRequire()) {
            return;
        }
        $required = (array)$this->composer->getConfig('require');
        foreach ($dependences as $name => $version) {
            if (is_int($name) || empty($version)) {
                continue;
            }
            if (isset($required[$name])) {
                $this->warn("Composer dependence \"$name\" already existed!");
                continue;
            }

            // 安装composer插件
            $this->composer->require($name, $version);
        }

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

        if ($this->composer->psr4NamespaceExist($this->plugin->getNamespace())) {
            // 已存在同样命名空间，插件已安装或存在同名插件
            return $this->error('The plugin has been installed!');
        }

        // 检测插件是否有效
        $this->plugin->valid();
        $this->info("The plugin is installable!");

        // 增加插件命名空间
        $this->addNamespace($name);

        // 复制资源文件
        $this->copyAssets();

        //保 存插件名称到配置文件
        $this->saveConfig($name);

        // 检测插件安装器
        if ($installer = $this->plugin->getInstaller()) {
            $installer->install($this->plugin);
        }

        // 最后一步安装composer插件
        $this->requireComposerDependences();

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
        $this->info("Remove assets success!");

        $this->composer->deletePsr4Namespace($namespace);
        $this->composer->dumpOptimized();
        $this->info("Remove namespace success!");


        if (!$this->plugin()->disable()) {
            throw new InvalidArgumentException("Uninstall plugin fialed!");
        }

        // 检测插件安装器
        if ($installer = $this->plugin->getInstaller()) {
            $installer->uninstall($this->plugin);
        }

        $this->info("Uninstalled!");
    }


}
