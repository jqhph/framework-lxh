<?php
/**
 * comment
 *
 * @author admin
 * @date   2017/5/8 16:41
 */

namespace Lxh\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;

abstract class GeneratorCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Lxh\File\FileManager
     */
    protected $files;

    /**
     * author
     *
     * @var string
     */
    protected $author = 'Jqh';

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
    public function __construct(Container $container)
    {
        parent::__construct();

        $this->files = $container->make('file.manager');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $name = $this->parseName($this->getNameInput());

        $path = $this->getPath($name);

        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        file_put_contents($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
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
        $name = str_replace($this->getApplication()->getNamespace(), '', $name);

        return $this->getApplication()->getBasePath() . $this->folder . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $rootNamespace = $this->getApplication()->getNamespace();

        if (strpos($name, $rootNamespace) !== false) {
            return $name;
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getfileNamespace(trim($rootNamespace, '\\')) . "{$this->fileNamespace}\\$name");
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getfileNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! is_dir(dirname($path))) {
            $this->files->mkdir(dirname($path), 0777, true);
        }
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
                '{namespace}'      => $this->getNamespace($name),
                '{root-namespace}' => $this->getApplication()->getNamespace(),
                '{class}'          => str_replace($this->getNamespace($name).'\\', '', $name),
                '{date}'           => date('Y-m-d H:i:s'),
                '{author}'         => $this->author
            ]
        );
    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->normalizeClass($this->argument('name'));
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

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }
}
