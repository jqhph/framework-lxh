<?php

namespace Lxh\Crontab;

use Symfony\Component\Console\Input\InputArgument;

class Test extends Handler
{
    public function handle()
    {
        $this->line($this->argument('name'));

        $this->line($this->argument('age'));

        $this->line(['name' => 'zs', 'age' => 23]);

        $this->line($this->usetime());
    }

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller.'],
            ['age', InputArgument::OPTIONAL, 'The name of the controller.'],
        ];
    }
}
