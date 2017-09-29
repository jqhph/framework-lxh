<?php
namespace Lxh\Container;

use Lxh\Exceptions\BindingResolutionException;
use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container AS ContractsContainer;
use Lxh\Helper\Util;
use Lxh\Support\Arr;
use ArrayAccess;
use Closure;

class Container implements ArrayAccess, ContractsContainer
{
    use Loader;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @var array
     */
    protected $buildStack = [];

    /**
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * @var array
     */
    protected $reboundCallbacks = [];

    /**
     * @var array
     */
    protected $extenders = [];

    /**
     * @var array
     */
    protected $globalResolvingCallbacks = [];

    /**
     * @var array
     */
    protected $resolvingCallbacks = [];

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
     */
    public function make($abstract)
    {
        return isset($this->instances[$abstract]) ? $this->instances[$abstract] : $this->resolve($abstract);
    }

    /**
     * 保存一个实例到容器
     *
     * @param string $abstract 别名、类名
     * @param object $instance 对象
     * @return void
     */
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
        if ($concrete instanceof Closure) {
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

            throw new BindingResolutionException($message);
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

        array_pop($this->buildStack);

        return $reflector->newInstanceArgs($this->resolveDependencies($constructor->getParameters()));
    }

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure)
    {
        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);

            $this->rebound($abstract);
        } else {
            $this->extenders[$abstract][] = $closure;
        }
    }


    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) ||
        isset($this->instances[$abstract]);
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return mixed
     */
    protected function resolve($name)
    {
        $object = '';

        if ($binding = $this->getServiceBindings($name)) {
            $abstract = & $name;
        } else {
            $abstract = $this->normalize($name);

            if (isset($this->instances[$abstract])) return $this->instances[$abstract];

            $binding = $this->getServiceBindings($abstract);
        }

        if ($binding) {
            $object = $this->resolveWithBindings($abstract, $binding);
        }

        if (! $object) {
            $object = empty($binding['closure']) ? $this->build($abstract) : $binding['closure']($this);
        }

        // If we defined any extenders for this type, we'll need to spin through them
        // and apply them to the object being built. This allows for the extension
        // of services, such as changing configuration or decorating the object.
        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }

        if ($this->isShared($abstract)) {
            $this->instances[get_class($object)] = $this->instances[$abstract] = $this->instances[$name] = $object;
        }

        $this->fireResolvingCallbacks($abstract, $object);

        // Before returning, we will also set the resolved flag to "true" and pop off
        // the parameter overrides for this build. After those two things are done
        // we will be ready to return back the fully constructed class instance.
        $this->resolved[$abstract] = true;

        return $object;
    }

    /**
     * Register a new resolving callback.
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, Closure $callback = null)
    {
        if ($abstract instanceof Closure) {
            $this->globalResolvingCallbacks[] = $abstract;
        } else {
            $this->resolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function resolveDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as &$parameter) {
            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            $dependencies[] = $this->resolveClass($parameter);
        }

        return $dependencies;
    }


    /**
     * Fire all of the resolving callbacks.
     *
     * @param  string  $abstract
     * @param  mixed   $object
     * @return void
     */
    protected function fireResolvingCallbacks($abstract, $object)
    {
        foreach ($this->globalResolvingCallbacks as $callback) {
            $callback($object, $this);
        }

        foreach ($this->getCallbacksForType($abstract, $object, $this->resolvingCallbacks) as $callback) {
            $callback($object, $this);
        }
    }

    /**
     * Get all callbacks for a given type.
     *
     * @param  string  $abstract
     * @param  object  $object
     * @param  array   $callbacksPerType
     *
     * @return array
     */
    protected function getCallbacksForType($abstract, $object, array & $callbacksPerType)
    {
        $results = [];

        foreach ($callbacksPerType as $type => & $callbacks) {
            if ($type === $abstract || $object instanceof $type) {
                $results = array_merge($results, $callbacks);
            }
        }

        return $results;
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
     * Register a binding in the container.
     *
     * @param string|array $abstract 类名 或 类名=>别名
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared 是否单例
     * @return void
     */
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

        // If the abstract type was already resolved in this container we'll fire the
        // rebound listener so that any objects which have already gotten resolved
        // can have their copy of the object updated via the listener callbacks.
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    /**
     * Bind a new callback to an abstract's rebind event.
     *
     * @param  string    $abstract
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rebinding($abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;

        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
    }

    /**
     * Refresh an instance on the given target and method.
     *
     * @param  string  $abstract
     * @param  mixed   $target
     * @param  string  $method
     * @return mixed
     */
    public function refresh($abstract, $target, $method)
    {
        return $this->rebinding($abstract, function ($app, $instance) use ($target, $method) {
            $target->{$method}($instance);
        });
    }

    protected function normalize(& $name)
    {
        return preg_replace_callback('/([A-Z])/', [$this, 'convert'], $name);
    }

    protected function convert(& $text)
    {
        return '.' . strtolower($text[1]);
    }

    /**
     * Fire the "rebound" callbacks for the given abstract type.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            call_user_func($callback, $this, $instance);
        }
    }

    /**
     * Get the extender callbacks for a given type.
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getExtenders($abstract)
    {
        if (isset($this->extenders[$abstract])) {
            return $this->extenders[$abstract];
        }

        return [];
    }

    /**
     * Get the rebound callbacks for a given type.
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        if (isset($this->reboundCallbacks[$abstract])) {
            return $this->reboundCallbacks[$abstract];
        }

        return [];
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
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
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
        unset($this->instances[$abstract]);
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

    public function flush($except = [])
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

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->bound($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->instances[$key]) ? $this->instances[$key] : $this->resolve($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     * 从服务容器中获取服务实例
     *
     * @param string $key 小驼峰写法会自动转化为“.”格式，如：
     *               httpRequest => http.request
     *
     * @return object
     */
    public function __get($key)
    {
        return isset($this->instances[$key]) ? $this->instances[$key] : $this->resolve($key);
    }
}
