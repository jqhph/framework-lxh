<?php

namespace Lxh\Crontab;

use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\InvalidArgumentException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application
{
    protected $container;

    protected $crontabName;

    protected $argv;

    public function __construct(Container $container)
    {
        $this->container = $container;

        if (count($_SERVER['argv']) < 2) {
            throw new InvalidArgumentException('The crontab defined cannot have an empty name.');
        }

        $this->setCrontabName($_SERVER['argv'][1]);

    }

    protected function setCrontabName($name)
    {
        $this->crontabName = str_replace('.', '/', $name);
    }


    public function handle()
    {
        $cron = $this->createCrontab();

        array_shift($_SERVER['argv']);

        $this->argv = $this->createArgvInput(
            (array) $cron->getArguments(),
            (array) $cron->getOptions(),
            $_SERVER['argv']
        );

        $cron->argv($this->argv);

        return $cron->handle();
    }

    /**
     * 定时任务
     *
     * @return Handler
     */
    protected function createCrontab()
    {
        $class = "Lxh\\Crontab\\{$this->crontabName}";

        return new $class($this->container, $this);
    }

    /**
     * ArgvInput represents an input coming from the CLI arguments.
     *
     * @param array $arguments
     * @param array $options
     * @return ArgvInput
     */
    protected function createArgvInput(array $arguments = [], array $options = [], array $argv = [])
    {
        $definition = new InputDefinition();

        $definition->setArguments($this->createInput(InputArgument::class, $arguments));
        $definition->setOptions($this->createInput(InputOption::class, $options));

        return new ArgvInput($argv, $definition);
    }


    protected function createInput($class, array $config)
    {
        $reflector = new \ReflectionClass($class);

        $data = [];
        foreach ($config as & $v) {
            $options[] = $reflector->newInstanceArgs($v);
        }
print_r($options);
        return $data;
    }


}
