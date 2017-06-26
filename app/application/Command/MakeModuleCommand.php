<?php
/**
 * comment
 *
 * @author Jqh
 * @date   2017-06-14 11:11:35
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;

class MakeModuleCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a module';


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
        $this->line("Time-consuming: " . (microtime(true) - CONSOLE_START));
        // TODO
        $name = $this->argument('name');

        $app = $this->getApplication();

        $app->call('make:controller', [$name]);

        $app->call('make:model', [$name]);

        $this->info("The module $name created successfully.");

        $this->line("Time-consuming: " . (microtime(true) - CONSOLE_START));

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
