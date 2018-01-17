<?php

namespace Lxh\Container;

use Lxh\Auth\AuthManager;
use Lxh\Exceptions\BindingResolutionException;
use Lxh\Exceptions\InternalServerError;
use Lxh\Exceptions\InvalidArgumentException;
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
            'class' => 'Lxh\Container\Container',
            'shared' => true,
        ],
        'config' => [
            'shared' => true,
            'class' => 'Lxh\Config\Config'
        ],
        'events' => [
            'class' => 'Lxh\Events\Dispatcher',
            'dependencies' => 'container',
            'shared' => true,
            'aliases' => [
                EventsDispatcher::class,
            ]
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
            'dependencies' => ['container'],
        ],
        'auth.manager' => [
            'shared' => true,
            'class' => AuthManager::class,
        ],
        'controller.manager' => [
            'shared' => true,
            'class' => 'Lxh\MVC\ControllerManager',
            'dependencies' => ['container', 'http.request', 'http.response', 'pipeline', 'events']
        ],
        'files' => [
            'shared' => true,
            'class' => 'Lxh\File\FileManager',
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
            ],
            'aliases' => \Psr\Http\Message\ResponseInterface::class
        ],
        'http.request' => [
            'shared' => true,
            'class' => 'Lxh\\Http\\Request',
            'aliases' => \Psr\Http\Message\RequestInterface::class
        ],
        'pipeline' => [
            'shared' => false,
            'class' => 'Lxh\Pipeline\Pipeline',
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
        'crontab' => [
            'shared' => true,
            'class' => 'Lxh\Crontab\Application',
            'dependencies' => 'container'
        ],
        'view.adaptor' => [
            'shared' => true,
            'class' => 'Lxh\Template\Factory',
            'dependencies' => 'container'
        ],
        'view' => [
            'shared' => true,
            'class' => 'Lxh\Template\View',
            'dependencies' => 'controller.manager'
        ],
        'view.factory' => [
            'provider' => ViewServiceProvider::class,
        ],
        'mailer' => [
            'provider' => MailServiceProvider::class
        ],
        'track' => [
            'shared' => true,
            'class' => 'Lxh\Debug\Track',
            'dependencies' => 'container'
        ],
        'translator' => [
            'shared' => true,
            'class' => 'Lxh\Language\Translator',
            'dependencies' => 'container'
        ],
        'shutdown' => [
            'shared' => true,
            'class' => 'Lxh\Debug\Shutdown',
            'dependencies' => ['container', 'events', 'http.response']
        ],
        'error.handler' => [
            'shared' => true,
            'class' => 'Lxh\Debug\Error',
        ],
        'session' => [
            'shared' => true,
            'class' => 'Lxh\Session\Store',
        ],
        'cookie' => [
            'shared' => true,
            'class' => 'Lxh\Cookie\Store',
        ],
        'admin' => [
            'shared' => true,
            'class' => 'Lxh\Admin\Admin',
        ],
        'url' => [
            'shared' => false,
            'class' => 'Lxh\Http\Url',
        ],
        'plugin.manager' => [
            'shared' => true,
            'class' => 'Lxh\Plugins\Manager',
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
            $this->mergeAllBindings();
            $this->resolvedConfig = true;
        }

        return isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : [];
    }

    /**
     * 合并容器注册配置数组
     *
     * @return void
     */
    public function mergeAllBindings()
    {
        $this->bindings += (array) include __ROOT__ . 'config/container/container.php';

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
