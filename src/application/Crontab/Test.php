<?php

namespace Lxh\Crontab;

use Symfony\Component\Console\Input\InputArgument;

class Test extends Handler
{
    public function handle()
    {
        echo $this->argument('name');
    }

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller.'],
        ];
    }
}
