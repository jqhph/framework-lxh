<?php

namespace Lxh\Container;

use Lxh\Exceptions\InternalServerError;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\View\ViewServiceProvider;

//加载器
trait Loader
{
    protected $container;

    /**
     * 是否载入配置文件
     */
    protected $isLoadConfig;

    /**
     * 服务注册数组
     *
     * @key 服务别名
     * @value ===>
     * 			   class string  类名
     *	   		   dependencies array|string  依赖的服务 ===>
     *									 	   服务别名1,  服务别名2 ...
     */
    protected $bindings = [//加载器加载信息（优先查找load*方法，找不到再从此数组信息中找）
        'container' => [
            'class' => 'Lxh\Container\Container',
            'shared' => true,
        ],
        'config' => [
            'class' => 'Lxh\Config\Config'
        ],
        'events' => [
            'class' => 'Lxh\Events\Dispatcher',
            'dependencies' => 'container',
            'shared' => true,
        ],
        'http.input' => [
            'shared' => true,
            'class' => 'Lxh\\Http\\Input',
        ],
        'redis' => [
            'shared' => true,
            'class' => 'Lxh\ORM\Connect\Redis',
        ],
        'query' => [
            'shared' => false,
            'class' => 'Lxh\ORM\Query',
            'dependencies' => ['builder.manager', 'container']
        ],
//            'router' => [
//                'shared' => true,
//                'class' => 'Lxh\Router\Dispatcher',
//                'dependencies' => 'container'
//            ],
        'controller.manager' => [
            'shared' => true,
            'class' => 'Lxh\MVC\ControllerManager',
            'dependencies' => ['container', 'http.request', 'http.response', 'pipeline', 'events']
        ],
        'file.manager' => [
            'shared' => true,
            'class' => 'Lxh\File\FileManager',
            'dependencies' => 'config'
        ],
        'http.client' => [
            'shared' => false,
            'class' => 'Lxh\\Http\\Client'
        ],
        'http.response' => [
            'shared' => true,
            'class' => 'Lxh\\Http\\Response',
            'dependencies' => [
                'http.request', 'container'
            ]
        ],
//            'http.header' => [
//                'shared' => true,
//                'class' => 'Lxh\\Http\\Header',
//            ],
        'http.request' => [
            'shared' => true,
            'class' => 'Lxh\\Http\\Request'
        ],
        'pipeline' => [
            'shared' => false,
            'class' => 'Lxh\Pipeline\Pipeline',
            'dependencies' => 'container'
        ],
        'builder.manager' => [
            'shared' => true,
            'class' => 'Lxh\ORM\Driver\BuilderManager',
            'dependencies' => 'container'
        ],
        'logger' => [
            'shared' => true,
            'class' => 'Lxh\Logger\Manager',
            'dependencies' => 'container'
        ],
        'model.factory' => [
            'shared' => true,
            'class' => 'Lxh\MVC\ModelFactory',
            'dependencies' => 'container'
        ],
        'exception.handler' => [
            'shared' => true,
            'class' => 'Lxh\Exceptions\Handlers\Handler',
            'dependencies' => ['logger', 'http.request', 'http.response', 'events']
        ],
        'validator' => [
            'shared' => true,
            'class' => 'Lxh\Helper\Valitron\Validator'
        ],
        'mongo' => [
            'shared' => true,
            'class' => 'Lxh\ORM\DB\Mongo\Connection'
        ],
        'console' => [
            'shared' => true,
            'class' => 'Lxh\Console\Application',
            'dependencies' => 'container'
        ],
        'view' => [
            'shared' => true,
            'class' => 'Lxh\Template\View',
            'dependencies' => 'container'
        ],
        'view.factory' => [
            'provider' => ViewServiceProvider::class,
        ],
        'track' => [
            'shared' => true,
            'class' => 'Lxh\Debug\Track',
            'dependencies' => 'container'
        ],
        'language.manager' => [
            'shared' => true,
            'class' => 'Lxh\Language\Manager',
            'dependencies' => 'container'
        ],
        'shutdown' => [
            'shared' => true,
            'class' => 'Lxh\Debug\Shutdown',
            'dependencies' => ['container', 'events', 'http.response']
        ],
    ];

    /**
     * 载入服务实例并注册到服务容器上
     *
     * @param $abstract string 载入类实例的别名
     * @return object
     */
    public function load($abstract, $binding)
    {
        if (empty($binding['class']) && empty($binding['provider'])) {
            return false;
        }

        if (! empty($binding['provider'])) {
            return $this->registerWithProvider($abstract, $binding['provider']);
        }

        $className = $binding['class'];

        $dependencies = isset($binding['dependencies']) ? (array) $binding['dependencies'] : [];

        switch (count($dependencies)) {
            case 0:
                return new $className();

            case 1:
                return new $className($this->getDependencyInstance($dependencies[0]));

            case 2:
                return new $className(
                    $this->getDependencyInstance($dependencies[0]),
                    $this->getDependencyInstance($dependencies[1])
                );

            case 3:
                return new $className(
                    $this->getDependencyInstance($dependencies[0]),
                    $this->getDependencyInstance($dependencies[1]),
                    $this->getDependencyInstance($dependencies[2])
                );

            default:
                return $this->getServiceInstance($className, $dependencies);

        }
    }

    /**
     * 服务提供者
     *
     * @param string $abstract
     * @param string $provider
     * @return object
     */
    protected function registerWithProvider($abstract, $provider)
    {
        $provider = new $provider($this);

        $provider->register();

        return $this->make($abstract);
    }

    /**
     * 获取依赖类实例
     *
     * @param string $alias 别名
     * @return object
     */
    protected function getDependencyInstance($alias)
    {
        return $this->make($alias);
    }

    /**
     * 根据别名获取类名
     *
     * @param string $alias
     * @return string
     */
    protected function getDependencyClass($alias)
    {
        $loadRules = $this->getAllBindings();
        if (! isset($loadRules[$alias])) {
            throw new InternalServerError('找不到依赖类信息');
        }

        return $loadRules[$alias]['class'];
    }

    /**
     * 利用反射获取服务实例
     * @param $className string 要载入的类名
     * @param $params array $className类依赖的参数
     */
    protected function getServiceInstance($className, array & $dependencies = [])
    {
        $class = new \ReflectionClass($className);

        foreach ($dependencies as & $abstract) {
            $abstract = $this->getDependencyInstance($abstract);
        }

        return $class->newInstanceArgs($dependencies);
    }

    /**
     * 获取注入详情
     */
    protected function getServiceBindings($abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract];
        }

        $bindings = $this->getAllBindings();

        if (! isset($bindings[$abstract])) {
            return false;
        }

        return $bindings[$abstract];
    }

    /**
     * 检查别名是否存在
     *
     * @return bool
     */
    public function loadAliasExists($alias)
    {
        $loadRules = $this->getAllBindings();
        return isset($loadRules[$alias]);
    }

    /**
     * 获取服务注册配置数组
     */
    public function getAllBindings()
    {
        if (! $this->isLoadConfig) {
            $this->bindings += $this->make('config')->getContainerConfig();
            $this->isLoadConfig = true;
        }
        return $this->bindings;
    }

}
