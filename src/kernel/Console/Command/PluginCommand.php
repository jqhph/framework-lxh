<?php

namespace Lxh\Console\Command;

use Lxh\Console\Command;
use Lxh\Helper\Util;
use Lxh\Plugins\Installer;
use Lxh\Plugins\Plugin;
use Lxh\Support\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage plugins.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $installer = $this->installer();
        $installer->setOutput($this);

        // 同步资源文件
        if ($this->option('sync')) {
            $installer->copyAssets();
            return;
        }

        // 禁用插件
        if ($this->option('disable')) {
            $this->disable();
            return;
        }

        // 启用插件
        if ($this->option('enable')) {
            $this->enable();
            return;
        }

        // 创建插件
        if ($this->option('create')) {
            $this->create();

            if ($this->option('install')) {
                $installer->install();
            }
            return;
        }

        // 安装插件
        if ($this->option('install')) {
            $installer->install();
            return;
        }

        // 卸载插件
        if ($this->option('uninstall')) {
            $installer->uninstall();
            return;
        }

        $this->info(
            $installer->plugin()->string()
        );
    }

    /**
     * 禁用插件
     *
     * @return void
     */
    protected function disable()
    {
        $name = $this->formatPluginName();

        $plugin = resolve('plugin.manager')->plugin($name);

        if ($plugin->disable()) {
            $this->info('Success!');
            return;
        }
        $this->error('Failed!');
    }

    /**
     * 启用插件
     *
     * @return void
     */
    protected function enable()
    {
        $name = $this->formatPluginName();

        $plugin = resolve('plugin.manager')->plugin($name);

        $namespace = $this->formatNamespace();

        $composer = new Composer();
        if (!$composer->psr4NamespaceExist($namespace)) {
            $this->error('Please install the plugin first!');
            return;
        }

        if ($plugin->enable()) {
            $this->line('Success!');
            return;
        }
        $this->error('Failed!');
    }

    /**
     * @return void
     */
    protected function create()
    {
        $name = $this->formatPluginName();
        $namespace = $this->formatNamespace();
        $files = files();

        $plugin = resolve('plugin.manager')->plugin($name);

        // 插件根目录
        $basePath = $plugin->getPath();

        if (file_exists($basePath)) {
            return $this->error('The plugin already exist!');
        }

        // 创建插件配置文件
        if (
         ! $files->putContents($plugin->getConfigPath(), $this->buildConfig($name, $namespace))
        ) {
           return $this->error('Build plugin config faild!');
        }
        $this->info('Build plugin config success!');

        // 创建插件注册类
        if (
        ! $files->putContents($plugin->getApplicationPath(), $this->buildRegister($name, $namespace))
        ) {
            return $this->error('Build plugin register class faild!');
        }
        $this->info('Build plugin register class success!');

        // 创建插件安装类
        if (
        ! $files->putContents($plugin->getInstallerPath(), $this->buildInstaller($name, $namespace))
        ) {
            return $this->error('Build plugin installer class faild!');
        }
        $this->info('Build plugin installer class success!');

        // 创建资源文件目录
        $files->mkdir($basePath . '/assets/js');
        $files->mkdir($basePath . '/assets/css');
        $files->mkdir($basePath . '/assets/images');
        $this->info('Build assets dir success!');

        // 创建控制器和模型文件夹
        $files->mkdir($basePath . '/src/Http/Controllers');
        $files->mkdir($basePath . '/src/Http/Models');
        $files->mkdir($basePath . '/src/Http/Middlewares');
        $this->info('Build Http class dir success!');

        // 创建模板文件夹
        $files->mkdir($basePath . '/views');
        $this->info('Build views dir success!');

        $this->info('Created!');
    }


    /**
     * @param $name
     * @param $namespace
     * @return string
     */
    protected function buildInstaller($name, $namespace)
    {
        return strtr(
            file_get_contents($this->getInstallerStub()),
            [
                '{namespace}' => $namespace,
                '{name}'      => $name,
            ]
        );
    }

    /**
     * @param $name
     * @param $namespace
     * @return string
     */
    protected function buildRegister($name, $namespace)
    {
        return strtr(
            file_get_contents($this->getRegisterStub()),
            [
                '{namespace}' => $namespace,
                '{name}'      => $name,
            ]
        );
    }

    /**
     * Build the config with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildConfig($name, $namespace)
    {
        return strtr(
            file_get_contents($this->getConfigStub()),
            [
                '{namespace}' => $namespace,
                '{name}'      => $name,
                '{date}'      => date('Y-m-d H:i:s'),
            ]
        );
    }

    protected function getRegisterStub()
    {
        return __DIR__ . '/stubs/plugin-register.stub';
    }

    protected function getInstallerStub()
    {
        return __DIR__ . '/stubs/plugin-installer.stub';
    }

    protected function getConfigStub()
    {
        return __DIR__ . '/stubs/plugin-config.stub';
    }

    /**
     * 驼峰转化为小写中划线
     *
     * @return string
     */
    protected function formatPluginName()
    {
        return lc_dash($this->argument('name'));
    }

    /**
     * 中划线转化为驼峰
     *
     * @return mixed|string
     */
    protected function formatNamespace()
    {
        return ucfirst(camel_case($this->formatPluginName(), '-'));
    }

    /**
     * @return Installer
     */
    protected function installer()
    {
        return resolve('plugin.manager')->installer($this->formatPluginName());
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of plugin.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['install', '', InputOption::VALUE_NONE, 'Install plugin.'],
            ['uninstall', '', InputOption::VALUE_NONE, 'Uninstall plugin.'],
            ['create', '', InputOption::VALUE_NONE, 'Create a empty value template.'],
            ['disable', '', InputOption::VALUE_NONE, 'Disable use the plugin.'],
            ['enable', '', InputOption::VALUE_NONE, 'Enable use the plugin.'],
            ['sync', '', InputOption::VALUE_NONE, 'Copy assets to webserver path.'],
        ];
    }

}
