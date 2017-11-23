<?php

namespace Lxh\Console\Command;

use Lxh\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ConfigCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'config:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster configuration loading';

    protected $allows = [ENV_PROD, ENV_DEV, ENV_TEST];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $config = resolve('config');
        $env = $this->getEnv();
        if (! $env) {
            $allows = implode(', ', $this->allows);
            return $this->error("Invalid env options [$allows]");
        }

        // 先清除缓存
        $config->env($env)->removeCache();

        $config->refetch();

        if ($config->saveCache() !== false) {
            $this->info('Configuration cached successfully!');
            return;
        }
        $this->error('Configuration cached failed!');

    }

    protected function getEnv()
    {
        $env = $this->option('env') ?: ENV_PROD;

        if (! in_array($env, $this->allows)) {
            return false;
        }
        return $env;
    }
}
