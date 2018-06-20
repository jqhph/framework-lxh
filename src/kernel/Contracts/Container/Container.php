<?php
namespace Lxh\Contracts\Container;

use Closure;

/**
 * Class Container
 *
 * @property \Lxh\Session\Store session
 * @property \Lxh\Cookie\Store  cookie
 * @property \Lxh\Config\Config config
 * @property \Lxh\Contracts\Events\Dispatcher events
 * @property \Lxh\Language\Translator translator
 * @property \Lxh\Router\Dispatcher router
 * @property \Lxh\Logger\Manager logger
 * @property \Lxh\Debug\Tracer tracer
 * @property \Lxh\Http\Url url
 * @property \Lxh\ORM\Query query
 * @property \Lxh\Cache\Factory cacheFactory
 * @property \Lxh\File\FileManager files
 * @property \Lxh\Http\Client httpClient
 * @property \Lxh\Http\Request request
 * @property \Lxh\Http\Response response
 * @property \Lxh\ORM\Connect\Redis redis
 * @property \Lxh\Mvc\ControllerManager controllerManager
 * @property \Lxh\Auth\AuthManager authManager
 * @property \Lxh\Pipeline\Pipeline pipeline
 * @property \Lxh\Mvc\ModelFactory modelFactory
 * @property \Lxh\Exceptions\Handlers\Handler exceptionHandler
 * @property \Lxh\Helper\Valitron\Validator validator
 * @property \Lxh\Console\Application console
 * @property \Lxh\Crontab\Application crontab
 * @property \Lxh\Template\Factory viewAdaptor
 * @property \Lxh\View\ViewServiceProvider viewFactory
 * @property \Lxh\Mail\MailServiceProvider mailer
 * @property \Lxh\Admin\Admin admin
 * @property \Lxh\Filters\Filter filters
 * @property \Lxh\Plugins\Manager pluginManager
 */
interface Container
{
	/**
	 * Determine if the given abstract type has been bound.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	public function bound($abstract);

	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return void
	*/
	public function instance($abstract, $instance);

	/**
	 * "Extend" an abstract type in the container.
	 *
	 * @param  string    $abstract
	 * @param  \Closure  $closure
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function extend($abstract, Closure $closure);

	/**
	 * Resolve the given type from the container.
	 *
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	*/
	public function make($abstract);
	
	/**
	 * Instantiate a concrete instance of the given type.
	 *
	 * @param  string  $concrete
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function build($concrete);


	/**
	 * Register a shared binding in the container.
	 *
	 * @param  string|array  $abstract
	 * @param  \Closure|string|null  $concrete
	 * @return void
	 */
	public function singleton($abstract, $concrete = null);

	/**
	 * Register a binding in the container.
	 *
	 * @param string|array $abstract 类名 或 类名=>别名
	 * @param  \Closure|string|null  $concrete
	 * @param  bool  $shared 是否单例
	 * @return void
	 * */
	public function bind($abstract, $concrete = null, $shared = false);

	/**
	 * Determine if a given type is shared.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	public function isShared($abstract);

	/**
	 * Determine if the given abstract type has been resolved.
	 *
	 * @param  string $abstract
	 * @return bool
	 */
	public function resolved($abstract);

	/**
	 * Register a new resolving callback.
	 *
	 * @param  string    $abstract
	 * @param  \Closure|null  $callback
	 * @return void
	 */
	public function resolving($abstract, Closure $callback = null);
}
