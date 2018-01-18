<?php

namespace Lxh\Support;

use Lxh\Exceptions\InvalidArgumentException;
use Lxh\File\FileManager;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;
use Lxh\Helper\Util;

class Composer
{
    /**
     * The filesystem instance.
     *
     * @var FileManager
     */
    protected $files;

    /**
     * The working path to regenerate from.
     *
     * @var string
     */
    protected $workingPath;

    /**
     *
     * @var string
     */
    protected $configPath;

    /**
     * @var array
     */
    protected $allowedAutoloadTypes = [
        'psr-4', 'psr-3'
    ];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Create a new Composer manager instance.
     *
     * @param  FileManager $files
     * @param  string|null $workingPath
     * @return void
     */
    public function __construct(FileManager $files = null, $workingPath = null)
    {
        $this->files = $files ?: files();
        $this->workingPath = $workingPath ?: config('composer.working-path');
        $this->setupComposerJsonPath();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function setupComposerJsonPath()
    {
        $this->configPath = __ROOT__ . 'composer.json';
        if (!is_file($this->configPath)) {
            throw new InvalidArgumentException('获取composer配置文件失败');
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        $config = $this->getConfig();

        return $this->saveConfig(Util::unsetInArray($config, $key));
    }

    /**
     * @param null $key
     * @param null $def
     * @return mixed
     * @throws \Lxh\Exceptions\Error
     */
    public function getConfig($key = null, $def = null)
    {
        if (! $this->config) {
            $json = $this->files->get($this->configPath);

            $this->config = json_decode($json, true);
        }

        return data_get($this->config, $key, $def);
    }

    /**
     * @param array $content
     * @return bool
     */
    public function merge(array $content)
    {
        $config = $this->getConfig();

        return $this->saveConfig(Util::merge($config, $content, true));
    }

    /**
     *
     * @param $namespace
     * @return bool
     */
    public function deletePsr4Namespace($namespace)
    {
        return $this->delete('autoload.psr-4.' . $this->normalizeNamespace($namespace));
    }

    /**
     * @param $namespace
     * @return string
     */
    protected function normalizeNamespace($namespace)
    {
        return trim($namespace, "\\") . "\\";
    }

    /**
     * @param $type
     * @param $namespace
     * @param $paths
     */
    public function addAutoloadNamespace($type, $namespace, $paths)
    {
        if (!in_array($type, $this->allowedAutoloadTypes)) {
            throw new InvalidArgumentException('不支持的自动加载类型');
        }

        return $this->merge([
            'autoload' => [
                $type => [
                    $this->normalizeNamespace($namespace) => (array) $paths
                ]
            ]
        ]);
    }

    /**
     * @param $namespace
     * @param $paths
     * @return bool
     * @throws InvalidArgumentException
     */
    public function addPsr4Namespace($namespace, $paths)
    {
        return $this->addAutoloadNamespace('psr-4', $namespace, $paths);
    }

    /**
     *
     * @return bool
     */
    public function psr4NamespaceExist($namespace)
    {
        return $this->getConfig("autoload.psr-4.{$this->normalizeNamespace($namespace)}") ? true : false;
    }

    /**
     * @param array $config
     * @return bool
     */
    public function saveConfig(array $config)
    {
        return $this->files->putContentsJson($this->configPath, $config);
    }

    /**
     * Regenerate the Composer autoloader files.
     *
     * @param  string  $extra
     * @return void
     */
    public function dumpAutoloads($extra = '')
    {
        exec(trim($this->findComposer().' dump-autoload '.$extra));

//        $process = $this->getProcess();
//
//        $process->setCommandLine(trim($this->findComposer().' dump-autoload '.$extra));
//
//        $process->run();
    }

    /**
     * Regenerate the optimized Composer autoloader files.
     *
     * @return void
     */
    public function dumpOptimized()
    {
        $this->dumpAutoloads('--optimize');
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (is_file($this->workingPath.'/composer.phar')) {
            return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false)).' composer.phar';
        }

        return 'composer';
    }

    /**
     * Get a new Symfony process instance.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess()
    {
        return (new Process('', $this->workingPath))->setTimeout(null);
    }

    /**
     * Set the working path used by the class.
     *
     * @param  string  $path
     * @return $this
     */
    public function setWorkingPath($path)
    {
        $this->workingPath = realpath($path);

        return $this;
    }

    public function __call($method, $arguments)
    {
        if ($method == 'require' && !empty($arguments[0])) {

            exec(trim($this->findComposer().' require '.$arguments[0]));
        }
    }
}
