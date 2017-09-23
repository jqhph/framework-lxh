<?php

namespace Lxh\Crontab;

use Lxh\Container\Container;
use Symfony\Component\Console\Input\ArgvInput;

abstract class Handler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Output
     */
    protected $output;

    /**
     * @var ArgvInput
     */
    protected $argv;
    
    public function __construct(Container $container, Application $app, $output = null)
    {
        $this->container = $container;
        $this->app = $app;

        $this->output = $output ?: new Output();
    }

    abstract public function handle();

    /**
     * 定时任务终止方法
     */
    public function terminate()
    {

    }

    public function line($msg, $count = 1)
    {
        $this->output->line($msg, $count);
    }

    public function newline($count = 1)
    {
        $this->output->newline($count);
    }

    /**
     * arguments配置
     *
     * @return array
     */
    public function getArguments()
    {
        return [];
    }

    /**
     * options配置
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    public function argv(ArgvInput $argv)
    {
        $this->argv = $argv;
    }

    /**
     * 获取argument参数
     *
     * @param string $k
     * @param mixed $d
     * @return mixed
     */
    public function argument($k, $d = null)
    {
        $v = $this->argv->getArgument($k);

        return $v ?: $d;
    }

    /**
     * 获取option参数
     *
     * @param string $k
     * @param mixed $d
     * @return mixed
     */
    public function option($k, $d = null)
    {
        $v = $this->argv->getOption($k);

        return $v ?: $d;
    }

    /**
     * 获取程序运行时间
     *
     * @return float
     */
    public function usetime()
    {
        return $this->app->usetime();
    }
}
