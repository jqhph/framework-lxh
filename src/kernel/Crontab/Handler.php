<?php

namespace Lxh\Crontab;

use Lxh\Container\Container;
use Symfony\Component\Console\Input\ArgvInput;

abstract class Handler
{
    protected $app;
    
    protected $container;

    /**
     * @var ArgvInput
     */
    protected $argv;
    
    public function __construct(Container $container, Application $app)
    {
        $this->container = $container;
        $this->app = $app;
    }

    abstract public function handle();

    public function getArguments()
    {
        return [];
    }

    public function getOptions()
    {
        return [];
    }

    public function argv(ArgvInput $argv)
    {
        $this->argv = $argv;
    }

    public function argument($k, $d = null)
    {
        $v = $this->argv->getArgument($k);

        return $v ?: $d;
    }

    public function option($k, $d = null)
    {
        $v = $this->argv->getOption($k);

        return $v ?: $d;
    }
}
