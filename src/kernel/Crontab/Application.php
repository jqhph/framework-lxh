<?php

namespace Lxh\Crontab;

use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Events\Dispatcher;
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

    protected $startTime = 0;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->startTime = microtime(true);
    }

    /**
     * 获取程序运行时间
     *
     * @return float
     */
    public function usetime()
    {
        return (round(microtime(true), 5) * 100000 - $this->startTime * 100000) / 100000;
    }

    public function handle($output = true, array & $argv = [])
    {
        $cron = $this->makeCrontab($output, $argv);

        $content = $cron->handle();

        $this->terminate($cron, $output);

        return $content;
    }

    /**
     *
     * @param $output
     * @param array $argv
     * @return Handler
     */
    protected function makeCrontab($output, array & $argv = [])
    {
        // 系统参数
        $argv = $this->normalizeArgv($argv);

        // 任务类名称
        $name = $this->normalizeName($argv[1]);

        $cron = $this->createCrontab($name);

        $this->setup($cron, $output, $argv);

        return $cron;
    }

    protected function normalizeName($name)
    {
        return str_replace('.', '\\', $name);
    }

    protected function normalizeArgv(array & $argv)
    {
        $argv = $argv ?: $_SERVER['argv'];

        if (count($argv) < 2) {
            throw new InvalidArgumentException('The crontab defined cannot have an empty name.');
        }

        return $argv;
    }

    /**
     * 获取任务类
     *
     * @param $crontabName
     * @param array $argv
     * @param bool $output 是否输出结果到客户端
     * @return Handler
     */
    public function make($crontabName, $argv = [], $output = false)
    {
        $argv || ($argv = (array) $argv);

        array_unshift($argv, $crontabName);
        array_unshift($argv, 'crontab');

        return $this->makeCrontab($output, $argv);
    }

    /**
     * 调用任务类
     *
     * @param string $crontabName
     * @param array $argv
     * @param bool $output 是否输出结果到客户端
     * @return mixed
     */
    public function call($crontabName, $argv = [], $output = false)
    {
        $argv || ($argv = (array) $argv);

        array_unshift($argv, $crontabName);
        array_unshift($argv, 'crontab');

        return $this->handle($output, $argv);
    }

    /**
     * 初始化
     *
     * @param Handler $cron
     * @param bool $output
     * @param array $argv
     */
    protected function setup(Handler $cron, $output, array $argv = [])
    {
        if ($output === false) {
            ob_start();
        }

        array_shift($argv);

        $this->argv = $this->createArgvInput(
            (array) $cron->getArguments(),
            (array) $cron->getOptions(),
            $argv
        );

        $cron->argv($this->argv);
    }

    /**
     * 定时任务终止方法
     *
     * @param Handler $cron
     * @param bool $output
     */
    protected function terminate(Handler $cron, $output)
    {
        $useTime = $this->usetime();
        $date = date('Y-m-d H:i:s');

        $cron->terminate();

        $cron->newline(2);
        $cron->line("[DATE: $date] [USETIME: {$useTime}]");

        if ($output === false) {
            ob_end_clean();
        }
    }

    /**
     * 定时任务
     *
     * @return Handler
     */
    protected function createCrontab($name)
    {
        $name = $name ?: $this->$this->crontabName;

        $class = "Lxh\\Crontab\\{$name}";

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


    /**
     * @param $class
     * @param array $config
     * @return array  InputArgument | InputOption
     */
    protected function createInput($class, array $config)
    {
        $reflector = new \ReflectionClass($class);

        $data = [];
        foreach ($config as & $v) {
            $data[] = $reflector->newInstanceArgs($v);
        }

        return $data;
    }

}
