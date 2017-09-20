<?php
/**
 * comment
 *
 * @author Jqh
 * @date   2017-06-14 08:59:35
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Lxh\Console\ModuleGeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;
use Lxh\Console\GeneratorCommand;

class MakeControllerCommand extends ModuleGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    protected $folder = 'application/';

    protected $fileNamespace = '\\Controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller';


    /**
     * The help information description.
     *
     * @var string
     */
    protected $help;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller.'],
            ['module', InputArgument::OPTIONAL, 'The name of the module.'],
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
//            ['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that should be assigned.', 'command:name'],
        ];
    }
}
