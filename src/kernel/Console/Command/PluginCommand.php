<?php

namespace Lxh\Console\Command;

use Lxh\Console\Command;
use Lxh\Plugins\Installer;
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
     * @var array
     */
    protected $allowedOperations = [
        'list', 'install', 'uninstall', 'installed'
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $installer = $this->installer();
        $installer->setOutput($this);

        if ($install = $this->option('install')) {
            $installer->install();
        }

        if ($uninstall = $this->option('uninstall')) {
            $installer->uninstall();
        }
        
        if (! $uninstall && ! $install) {
            $this->line(
                $installer->plugin()->string()
            );
        }
    }

    /**
     * @return Installer
     */
    protected function installer()
    {
        return resolve('plugin.manager')->installer($this->argument('name'));
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
        ];
    }

}
