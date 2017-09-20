<?php
/**
 * 生成中间件命令
 *
 * @author Jqh
 * @date   2017-06-14 07:59:39
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Lxh\Console\GeneratorCommand;
use Lxh\Console\ModuleGeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeMiddlewareCommand extends ModuleGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:middleware';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a middleware class';

    protected $folder = 'application/';

    protected $fileNamespace = '\\Middleware';

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
        return __DIR__.'/stubs/middleware.stub';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the middleware.'],
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
            ['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that should be assigned.', 'command:name'],
        ];
    }
}
