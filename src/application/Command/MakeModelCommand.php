<?php
/**
 * comment
 *
 * @author Jqh
 * @date   2017-06-14 09:04:21
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Lxh\Console\ModuleGeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;
use Lxh\Console\GeneratorCommand;

class MakeModelCommand extends ModuleGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';

    protected $folder = 'application/';

    protected $fileNamespace = '\\Models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model';


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
        return __DIR__.'/stubs/model.stub';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the model.'],
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
//            ['option-name', 'The alias.', InputOption::VALUE_OPTIONAL, 'A description text.', 'The default value.'],
        ];
    }
}
