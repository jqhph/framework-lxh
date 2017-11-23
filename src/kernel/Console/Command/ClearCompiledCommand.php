<?php

namespace Lxh\Console\Command;

use Lxh\Console\Command;

class ClearCompiledCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clear:compiled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the compiled class file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $servicesPath = __DATA_ROOT__ . config('view.compiled');

        if (file_exists($servicesPath)) {
            files()->remove($servicesPath);
        }

        $this->info("The compiled services file [$servicesPath] has been removed.");
    }
}
