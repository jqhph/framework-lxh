<?php
/**
 * 文件生成器
 *
 * @author Jqh
 * @date   2017/7/21 17:23
 */

namespace Lxh\Kernel\Builder\Driver;

use Lxh\Kernel\Builder\CodeGenerator;
use \Lxh\File\FileManager;

abstract class FileGenerator extends Creator
{
    /**
     * The filesystem instance.
     *
     * @var FileManager
     */
    protected $files;

    /**
     * author
     *
     * @var string
     */
    protected $author = 'Jqh';

    /**
     * Stub file name
     *
     * @var string
     */
    protected $defaultStub;

    /**
     * file folder
     *
     * @var string
     */
    protected $folder = '';

    /**
     * default namespace
     *
     * @var string
     */
    protected $fileNamespace = '';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new controller creator command instance.
     *
     * @param  Container  $container
     * @return void
     */
    public function __construct(CodeGenerator $generator)
    {
        parent::__construct($generator);

        $this->files = file_manager();
    }

    /**
     * 生成文件
     *
     * @return mixed
     */
    public function make(array $options)
    {
        $this->setOptions($options);

        $data = $this->fire($options['controller_name']);

        // 保存生成结果
        $this->content($data);

        return $data;
    }

    protected function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub($name = null)
    {
        $name = $name ?: $this->defaultStub;

        return __DIR__ . "/stubs/$name.stub";
    }

    /**
     * Execute the program.
     *
     * @return bool|null
     */
    public function fire($name)
    {
        $name = $this->normalizeClass($name);

        $name = $this->parseName($name);

        $path = $this->getPath($name);

        if ($this->alreadyExists($name)) {
            $this->generator->setError('exists', [$this->type]);

            return false;
        }

        return $this->buildClass($name);

//        return $this->files->putContents($path, $data, LOCK_EX);

    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        $name = $this->parseName($rawName);

        return is_file($this->getPath($name));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->getRootNamespace(), '', $name);

        return $this->getBasePath() . $this->getFolder() . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * 设置文件存储文件夹
     *
     * @return string
     */
    protected function getFolder()
    {
        return $this->folder;
    }

    /**
     * 获取项目根目录
     *
     * @return string
     */
    protected function getBasePath()
    {
        return __ROOT__;
    }

    /**
     * 获取根命名空间
     *
     * @return string
     */
    protected function getRootNamespace()
    {
        return 'Lxh';
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $rootNamespace = $this->getRootNamespace();

        if (strpos($name, $rootNamespace) !== false) {
            return $name;
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getFileNamespace(trim($rootNamespace, '\\')) . "{$this->fileNamespace}\\$name");
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getFileNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        return strtr(
            file_get_contents($this->getStub()),
            [
                '{namespace}'      => $this->normalizeNamespace($name),
                '{root-namespace}' => $this->getRootNamespace(),
                '{class}'          => str_replace($this->normalizeNamespace($name).'\\', '', $name),
                '{date}'           => date('Y-m-d H:i:s'),
                '{author}'         => $this->getAuthor(),
            ]
        );
    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function normalizeNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Normalize a class name.
     *
     * @param $name string
     * @return string
     */
    protected function normalizeClass($name)
    {
        return $name;
    }
}
