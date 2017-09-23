<?php
/**
 * Create a new Console command
 *
 * @author admin
 * @date   2017/5/8 16:37
 */

namespace Lxh\Console\Command;

use Lxh\Console\GeneratorCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCommandCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:command';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Console command';

    protected $fileNamespace = '\\Command';

    protected $folder = 'application/';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Console command';


    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return str_replace('{command}', $this->option('command'), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/console.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Command';
    }

    /**
     * Normalize a class name.
     *
     * @param $name string
     * @return string
     */
    protected function normalizeClass($name)
    {
        return $name . 'Command';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
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
            ['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that should be assigned.', 'command:name'],
        ];
    }
}
