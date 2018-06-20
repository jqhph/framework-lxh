<?php

namespace Lxh\Container;

use Lxh\Auth\AuthManager;
use Lxh\Cache\Factory;
use Lxh\Exceptions\BindingResolutionException;
use Lxh\Mail\MailServiceProvider;
use Lxh\View\ViewServiceProvider;
use Lxh\Contracts\Events\Dispatcher as  EventsDispatcher;

trait Loader
{
    protected $container;

    /**
     * 是否载入配置文件
     *
     * @var bool
     */
    protected $resolvedConfig = false;

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
            'class' => \Lxh\Container\Container::class,
            'shared' => true,
        ],
        'config' => [
            'shared' => true,
            'class' => \Lxh\Config\Config::class
        ],
        'events' => [
            'class' => \Lxh\Events\Dispatcher::class,
            'dependencies' => 'container',
            'shared' => true,
            'aliases' => [
                EventsDispatcher::class,
            ]
        ],
        'redis' => [
            'shared' => true,
            'class' => \Lxh\ORM\Connect\Redis::class,
        ],
        'query' => [
            'shared' => false,
            'class' => \Lxh\ORM\Query::class,
            'dependencies' => ['container'],
        ],
        'authManager' => [
            'shared' => true,
            'class' => AuthManager::class,
        ],
        'controllerManager' => [
            'shared' => true,
            'class' => \Lxh\Mvc\ControllerManager::class,
            'dependencies' => ['container', 'request', 'response', 'pipeline', 'events', 'filters']
        ],
        'files' => [
            'shared' => true,
            'class' => \Lxh\File\FileManager::class,
        ],
        'httpClient' => [
            'shared' => false,
            'class' => \Lxh\Http\Client::class
        ],
        'response' => [
            'shared' => true,
            'class' => \Lxh\Http\Response::class,
            'dependencies' => [
                'request', 'container'
            ],
            'aliases' => \Psr\Http\Message\ResponseInterface::class
        ],
        'request' => [
            'shared' => true,
            'class' => \Lxh\Http\Request::class,
            'aliases' => \Psr\Http\Message\RequestInterface::class
        ],
        'pipeline' => [
            'shared' => false,
            'class' => \Lxh\Pipeline\Pipeline::class,
            'dependencies' => 'container'
        ],
        'logger' => [
            'shared' => true,
            'class' => \Lxh\Logger\Manager::class,
            'dependencies' => 'container'
        ],
        'modelFactory' => [
            'shared' => true,
            'class' => \Lxh\Mvc\ModelFactory::class,
            'dependencies' => 'container'
        ],
        'exceptionHandler' => [
            'shared' => true,
            'class' => \Lxh\Exceptions\Handlers\Handler::class,
            'dependencies' => ['logger', 'request', 'response', 'events']
        ],
        'validator' => [
            'shared' => true,
            'class' => \Lxh\Helper\Valitron\Validator::class
        ],
        'mongo' => [
            'shared' => true,
            'class' => \Lxh\ORM\Mongo\Connection::class
        ],
        'console' => [
            'shared' => true,
            'class' => \Lxh\Console\Application::class,
            'dependencies' => 'container'
        ],
        'crontab' => [
            'shared' => true,
            'class' => \Lxh\Crontab\Application::class,
            'dependencies' => 'container'
        ],
        'viewAdaptor' => [
            'shared' => true,
            'class' => \Lxh\Template\Factory::class,
            'dependencies' => 'container'
        ],
        'viewFactory' => [
            'provider' => ViewServiceProvider::class,
        ],
        'mailer' => [
            'provider' => MailServiceProvider::class
        ],
        'tracer' => [
            'shared' => true,
            'class' => \Lxh\Debug\Tracer::class,
            'dependencies' => 'container'
        ],
        'translator' => [
            'shared' => true,
            'class' => \Lxh\Language\Translator::class,
            'dependencies' => 'container'
        ],
        'errorHandler' => [
            'shared' => true,
            'class' => \Lxh\Debug\Error::class,
        ],
        'session' => [
            'shared' => true,
            'class' => \Lxh\Session\Store::class,
        ],
        'cookie' => [
            'shared' => true,
            'class' => \Lxh\Cookie\Store::class,
        ],
        'admin' => [
            'shared' => true,
            'class' => \Lxh\Admin\Admin::class,
        ],
        'url' => [
            'shared' => false,
            'class' => \Lxh\Http\Url::class,
        ],
        'pluginManager' => [
            'shared' => true,
            'class' => \Lxh\Plugins\Manager::class,
        ],
        'filters' => [
            'shared' => true,
            'class' => \Lxh\Filters\Filter::class,
            'dependencies' => ['container']
        ],
        'cacheFactory' => [
            'shared' => true,
            'class' => Factory::class,
        ],
    ];

    /**
     * The registered type aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Resolve the given type with array.
     *
     * @param string $abstract
     * @param array $binding
     * @return object
     */
    protected function resolveWithBindings($abstract, array & $binding)
    {
        if (empty($binding['class']) && empty($binding['provider'])) {
            return false;
        }

        if (! empty($binding['provider']) && empty($binding['class']) && ! $this->resolved($abstract)) {
            return $this->resolveWithProvider($abstract, $binding['provider']);
        }

        $className = $binding['class'];

        $dependencies = isset($binding['dependencies']) ? (array) $binding['dependencies'] : [];

        return $dependencies ? $this->resolveService($className, $dependencies) : new $className();
    }

    /**
     * 服务提供者
     *
     * @param string $abstract
     * @param string $provider
     * @return object
     */
    protected function resolveWithProvider($abstract, $provider)
    {
        if (! empty($this->serviceProviders[$abstract])) {
            throw new BindingResolutionException("Cant't resolve the targe [$abstract] with provider [$provider]");
        }
        $this->serviceProviders[$abstract] = new $provider($this);

        $this->serviceProviders[$abstract]->register();

        return $this->make($abstract);
    }


    /**
     * 利用反射获取服务实例
     *
     * @param $className string 要载入的类名
     * @param $params array $className类依赖的参数
     *
     * @return object
     */
    protected function resolveService($className, array & $dependencies = [])
    {
        $class = new \ReflectionClass($className);

        foreach ($dependencies as & $abstract) {
            $abstract = $this->make($abstract);
        }

        return $class->newInstanceArgs($dependencies);
    }

    /**
     * Get the container's bindings.
     *
     * @return array
     */
    protected function getServiceBindings($abstract)
    {
        if (! $this->resolvedConfig) {
            $this->mergeConfigs();
            $this->resolvedConfig = true;
        }

        return isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : [];
    }

    /**
     * 合并容器注册配置数组
     *
     * @return void
     */
    public function mergeConfigs()
    {
        $this->bindings += (array) include __ROOT__ . '/config/container.php';

        foreach ($this->bindings as $abstract => &$conf) {
            if (!isset($conf['aliases'])) {
                continue;
            }
            foreach ((array) $conf['aliases'] as &$alias) {
                if ($alias == $abstract) continue;
                $this->aliases[$alias] = $abstract;
            }
        }
    }

}
