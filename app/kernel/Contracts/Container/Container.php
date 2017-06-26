<?php
namespace Lxh\Contracts\Container;

use Closure;

interface Container
{

	/**
	 * Register a binding with the container.
	 *
	 * @param  string|array  $abstract
	 * @param  \Closure|string|null  $concrete
	 * @param  bool  $shared
	 * @return void
	*/
// 	public function bind($abstract, $concrete, $dependencies = '');

	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return void
	*/
	public function instance($abstract, $instance);


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
}
