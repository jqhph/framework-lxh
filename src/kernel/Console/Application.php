<?php
/**
 * 命令台应用
 *
 * @author Jqh
 * @date   2017/5/5 16:53
 */

namespace Lxh\Console;

use Lxh\Contracts\Container\Container;
use Lxh\Events\Dispatcher;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use RuntimeException;
use Lxh\Helper\Arr;
use Symfony\Component\Console\Input\ArgvInput;

//use \\Exception;

class Application extends SymfonyApplication
{
    /**
     * The output from the previous command.
     *
     * @var
     */
    protected $lastOutput;

    /**
     * 服务容器
     *
     * @var Container
     */
    public $container;

    /**
     * 命名空间前缀
     *
     * @var string
     */
    protected $namespace;

    /**
     * 根路径
     *
     * @var string
     */
    protected $basePath = __ROOT__;

    /**
     * 命令类路径数组
     *
     * @var array
     */
    protected $commandDir = [
        'application/Command',
        'kernel/Console/Command'
    ];

    /**
     * 命令类命名空间
     *
     * @var array
     */
    protected $commandNamespaces = [
        'Lxh\\Command\\',
        'Lxh\\Console\\Command\\'
    ];

    /**
     * 命令字符串和命令类的映射关系数组
     *
     * @var array
     */
    protected $commandClassesMap = [];

    /**
     * 是否已注册所有命令对象
     *
     * @var bool
     */
    protected $isRegistedAllCommands = false;

    /**
     * 版本号
     *
     * @var string
     */
    protected $version = '1.0.0-dev';

    public function __construct(Container $container)
    {
        parent::__construct('Gets Console', $this->version);

        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        $this->container = $container;

    }

    // 注册所有命令
    public function registAllCommands()
    {
        if ($this->isRegistedAllCommands) {
            return;
        }

        $files = $this->container->make('file.manager');

        foreach ($this->commandDir as & $dir) {
            foreach ($files->getFileList($this->basePath . $dir, false, 'php', true) as & $f) {
                $this->regist(rtrim($f, 'Command.php'));
            }
        }

        $this->isRegistedAllCommands = true;
    }

    /**
     * 设置根路径
     *
     * @param $path string 路径
     * @return $this
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * Neta the commands (registered in the given namespace if provided).
     *
     * The array keys are the full names and the values the command instances.
     *
     * @param string $namespace A namespace name
     *
     * @return Command[] An array of Command instances
     */
    public function all($namespace = null)
    {
        $this->registAllCommands();

        return parent::all($namespace);
    }

    /**
     * 获取根路径
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    // 添加命令文件夹
    public function addCommandDir($dir)
    {
        $this->commandDir[] = $dir;

        return $this;
    }

    /**
     * 增加命令类命名空间
     *
     * @param $namespace string 命令类命名空间
     * @return $this
     */
    public function addCommandNamespace($namespace)
    {
        $this->commandNamespaces[] = $namespace;

        return $this;
    }

    public function find($name)
    {
        if (! $this->has($name)) {
            // 如果找不到命令类，则注册相应的命令类进去
            $this->regist($name);
        }
        return parent::find($name);
    }

    // 注册命令类
    public function regist($name)
    {
        foreach ($this->commandNamespaces as & $namespace) {
            $class = $namespace . $this->normalizeCommandClass(
                    $this->getClassNameByCommandName($name)
                );

            if (class_exists($class)) {
                return $this->resolve($class);
            }
        }
    }

    // 根据命令字符获取命令类
    public function normalizeCommandClass($name)
    {
        $delimiters = [
            ':',
            '-',
        ];
        foreach ($delimiters as & $limiter) {
            if (strpos($name, $limiter) === false) {
                return $name . 'Command';
            }
            $tmp = explode($limiter, $name);

            $name = ucfirst($tmp[0]) . ucfirst($tmp[1]);
        }
        return $name . 'Command';
    }

    // 根据命令字符从映射数组中获取命令类
    public function getClassNameByCommandName($command)
    {
        return isset($this->commandClassesMap[$command]) ? $this->commandClassesMap[$command] : $command;
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = [])
    {
        $this->lastOutput = new BufferedOutput();

        $this->setCatchExceptions(false);

        array_unshift($parameters, $command);

        array_unshift($parameters, '');

        $result = $this->run(new ArgvInput($parameters), $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * 运行控制台应用
     *
     * @return int
     */
    public function handle()
    {
        return $this->run(null, null);
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        return $this->lastOutput ? $this->lastOutput->fetch() : '';
    }

    /**
     * Add a command, resolving through the application.
     *
     * @param  string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function resolve($command)
    {
        return $this->add($this->container->make($command));
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param  array|mixed  $commands
     * @return $this
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        foreach ($commands as & $command) {
            $this->resolve($command);
        }

        return $this;
    }

    /**
     * Get the default input definitions for the applications.
     *
     * This is used to add the --env option to every available command.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption($this->getEnvironmentOption());

        return $definition;
    }

    /**
     * Get the global environment option for the definition.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getEnvironmentOption()
    {
        $message = 'The environment the command should run under.';

        return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message);
    }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents($this->basePath . 'composer.json'), true);

        foreach ((array) Arr::get($composer, 'autoload.psr-4') as $namespace => & $path) {
            return $this->namespace = $namespace;
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

}
