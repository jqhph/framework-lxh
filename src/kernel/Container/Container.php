<?php
namespace Lxh\Container;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container AS ContractsContainer;
use Lxh\Support\Arr;

class Container extends Loader implements ContractsContainer
{
    /**
     * @var array
     */
    protected $instances = [];

    protected $buildStack;

    protected $serviceProviders;
    
    public function __construct()
    {
    	$this->instances[__CLASS__]
            = $this->instances['container']
            = $this->instances['Lxh\Contracts\Container\Container']
            = $this;
    }

    /**
     * 获取一个服务
     *
     * @param string $abstract 服务类名或别名
     * @return object
     * */
    public function make($abstract)
    {
        if (isset($this->instances[$abstract])) {
        	return $this->instances[$abstract];
        }

        $binding = $this->getServiceBindings($abstract);

        if (! $instance = $this->load($abstract, $binding)) {
            if (! empty($binding['closure'])) {
                $instance = $binding['closure']($this);
            } else {
                return $this->build($abstract);
            }
        }

        if ($this->isShared($abstract)) {
            $this->instances[get_class($instance)] = $this->instances[$abstract] = $instance;
        }
        return $instance;
    }

    /**
     * 保存一个实例到容器
     *
     * @param string $abstract 别名、类名
     * @param object $instance 对象
     * @return void
     * */
    public function instance($abstract, $instance)
    {
        if (! $abstract) {
            throw new InternalServerError('Name is empty, save the instance failure');
        }
        if (! is_object($instance)) {
            throw new InternalServerError('Variable type error');
        }
        $this->instances[$abstract] = $instance;
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  string  $concrete
     * @param  array   $parameters
     * @return mixed
     */
    public function build($concrete)
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        $reflector = new \ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if (! $reflector->isInstantiable()) {
            if (! empty($this->buildStack)) {
                $previous = implode(', ', $this->buildStack);

                $message = "Target [$concrete] is not instantiable while building [$previous].";
            } else {
                $message = "Target [$concrete] is not instantiable.";
            }

            throw new InternalServerError($message);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor)) {
            array_pop($this->buildStack);

            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->getDependencies($dependencies);

        array_pop($this->buildStack);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            $dependencies[] = $this->resolveClass($parameter);
        }

        return $dependencies;
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param  \ReflectionParameter  $parameter
     * @return mixed
     */
    protected function resolveClass(\ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register a service provider with the application.
     *
     * @param  object|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return object|string
     */
    public function register($provider, $force = false)
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $this->serviceProviders[get_class($provider)] = $provider;

        $provider->register();

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  object|string  $provider
     * @return object|null
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return isset($this->serviceProviders[$name]) ? $this->serviceProviders[$name] : null;
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string  $provider
     * @return object
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * Register a binding in the container.
     *
     * @param string|array $abstract 类名 或 类名=>别名
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared 是否单例
     * @return void
     * */
	public function bind($abstract, $concrete = null, $shared = false)
	{
	    // If the given types are actually an array, we will assume an alias is being
	    // defined and will grab this "real" abstract class name and register this
	    // alias with the container so that it can be used as a shortcut for it.
	    if (is_array($abstract)) {
	        list($abstract, $concrete) = $this->extractAlias($abstract);
	    }

	    // If no concrete type was given, we will simply set the concrete type to the
	    // abstract type. This will allow concrete type to be registered as shared
	    // without being forced to state their classes in both of the parameter.
	    $this->dropStaleInstances($abstract);

	    if (is_null($concrete)) {
	        $concrete = $abstract;
	    }

	    if ($concrete instanceof \Closure) {
	        $concrete = [
	        	'closure' => $concrete,
	            'shared'  => $shared
	        ];
	    }

	    if (is_string($concrete)) {
	    	$concrete = [
	    		'class'  => $concrete,
	    	    'shared' => $shared
	    	];
	    } else {
	    	$concrete['shared'] = $shared;
	    }

	    $this->bindings[$abstract] = $concrete;
	}

	/**
	 * Determine if a given type is shared.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	public function isShared($abstract)
	{
	    if (isset($this->instances[$abstract])) {
	        return true;
	    }

	    return empty($this->bindings[$abstract]['shared']) ? false : true;
	}

	/**
	 * Extract the type and alias from a given definition.
	 *
	 * @param  array  $definition
	 * @return array
	 */
	protected function extractAlias(array $definition)
	{
	    return [key($definition), current($definition)];
	}

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        if (isset($this->instances[$abstract])) {
            $class = get_class($this->instances[$abstract]);
            unset($this->instances[$class]);
        }
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param  string  $abstract
     * @param  string  $concrete
     * @return \Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function ($c) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';

            return $c->$method($concrete);
        };
    }

    /**
     * 检测服务是否已注入容器
     *
     * @return bool
     * */
    public function exist($abstract)
    {
        return isset($this->instances[$abstract]);
    }

    public function clear($except = [])
    {
        $new = [];
        foreach ((array) $except as & $e) {
            if (isset($this->instances[$e])) {
                $new[$e] = $this->instances[$e];

                $new[get_class($this->instances[$e])] = $this->instances[$e];
            }
        }
        $this->instances = [];
        $this->instances = & $new;
    }

// 	public function normalize($service)
// 	{
// 		return is_string($service) ? ltrim($service, '\\') : $service;
// 	}
	
	
    private function __clone() 
    {
    	
    }
}
