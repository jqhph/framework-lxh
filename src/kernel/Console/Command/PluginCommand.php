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

        // 创建控制器
        if ($controller = $this->option('controller')) {
            $this->createController($installer, $controller);
            return;
        }

        // 创建模型
        if ($model = $this->option('model')) {
            $this->createModel($installer, $model);
            return;
        }

        // 禁用插件
        if ($this->option('disable')) {
            $this->disable();
            return;
        }

        // 启用插件
        if ($this->option('enable')) {
            $this->enable($installer);
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
     * 创建控制器
     *
     * @param Installer $installer
     * @param $controller
     */
    public function createController(Installer $installer, $controller)
    {
        if (!$installer->isInstalled()) {
            $this->error('Please install the plugin first!');
            return;
        }

        list($namespace, $path) = $this->formatHttpClass($controller, 'Controllers');

        $this->createHttpClass($controller, $path, $namespace, 'controller');
    }

    /**
     * @param $name
     * @param $type
     * @return array
     */
    protected function formatHttpClass($name, $type)
    {
        list($sub, $controller) = $this->parseClassName($name);

        $plugin = resolve('plugin.manager')->plugin($this->formatPluginName());

        $namespace = $this->formatNamespace() . "\\Http\\{$type}{$sub}";
        $path = $plugin->getPath() . "/src/Http/{$type}{$sub}/$controller.php";

        return [$namespace, $path];
    }

    /**
     * @param $name
     * @return string
     */
    protected function parseClassName($name)
    {
        $names = explode('\\', $name);
        if (count($names) < 2) {
            return ['', $name];
        }

        $name = array_pop($names);

        return ['\\' . implode('\\', $names), $name];
    }

    /**
     * 创建模型
     */
    public function createModel(Installer $installer, $model)
    {
        if (!$installer->isInstalled()) {
            $this->error('Please install the plugin first!');
            return;
        }

        list($namespace, $path) = $this->formatHttpClass($model, 'Models');

        $this->createHttpClass($model, $path, $namespace, 'model');
    }

    /**
     * @param string $name 控制器、模型名称
     * @param string $path 路径
     * @param string $namespace 命名空间
     * @param string $type 类型
     */
    protected function createHttpClass($name, $path, $namespace, $type)
    {
        if (file_exists($path)) {
            $this->error('File already exist!');
            return;
        }

        $name = explode('\\', $name);
        $name = ucfirst(
            camel_case(
                camel_case(end($name)), '-'
            )
        );

        $content = strtr(
            file_get_contents(__DIR__ . "/stubs/plugin-{$type}.stub"),
            $this->variables($name, $namespace)
        );

        if (files()->putContents($path, $content)) {
            $this->info('Created!');
        } else {
            $this->error('Failed!');
        }
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
    protected function enable(Installer $installer)
    {
        $name = $this->formatPluginName();

        $plugin = resolve('plugin.manager')->plugin($name);

        if (!$installer->isInstalled()) {
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
            $this->variables($name, $namespace)
        );
    }

    /**
     * @param string $name
     * @param string $namepace
     * @return array
     */
    protected function variables($name = null, $namepace = null)
    {
        return [
            '{namespace}' => $namepace ?: $this->formatNamespace(),
            '{name}'      => $name ?: $this->formatPluginName(),
            '{date}'      => date('Y-m-d H:i:s'),
        ];
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
            $this->variables($name, $namespace)
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
            $this->variables($name, $namespace)
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
            ['controller', 'c', InputOption::VALUE_REQUIRED, 'Create a controller class.'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Create model class.'],
        ];
    }

}
