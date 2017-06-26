<?php
/**
 * 创建模板命令
 *
 * @author Jqh
 * @date   2017-06-15 08:09:06
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;

class MakeViewCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a view';


    /**
     * The help information description.
     *
     * @var string
     */
    protected $help;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The controller name.'],
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
            ['action', 'a', InputOption::VALUE_OPTIONAL, 'The action name.', ''],
            ['template', 't', InputOption::VALUE_OPTIONAL, 'The template name.', ''],
        ];
    }
}
