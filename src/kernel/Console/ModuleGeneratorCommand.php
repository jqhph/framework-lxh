<?php

namespace Lxh\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;

abstract class ModuleGeneratorCommand extends GeneratorCommand
{
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        return strtr(
            file_get_contents($this->getStub()),
            [
                '{namespace}'      => $this->getNamespace($name),
                '{root-namespace}' => $this->getApplication()->getNamespace(),
                '{class}'          => ucfirst(str_replace($this->getNamespace($name).'\\', '', $name)),
                '{date}'           => date('Y-m-d H:i:s'),
                '{author}'         => $this->author,
                '{module}'         => $this->module(),
            ]
        );
    }

    protected function module()
    {
        $m = $this->argument('module');

        return $m ?: $this->container['controllerManager']->moduleName();
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->getApplication()->getNamespace(), '', $name);

        return $this->getApplication()->getBasePath() . $this->folder . $this->module() . '/' .  str_replace('\\', '/', $name) . '.php';
    }

}
