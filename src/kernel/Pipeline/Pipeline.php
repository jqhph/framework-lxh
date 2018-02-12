<?php

namespace Lxh\Pipeline;

use Lxh\Contracts\Pipeline as PipelineInterface;
use Lxh\Contracts\Container\Container;
use Closure;

class Pipeline
{
	/**
	 * The container implementation.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The object being passed through the pipeline.
	 *
	 * @var mixed
	 */
	protected $passable;

	/**
	 * The array of class pipes.
	 *
	 * @var array
	 */
	protected $pipes = [];

	/**
	 * The method to call on each pipe.
	 *
	 * @var string
	 */
	protected $method = 'handle';

	/**
	 * Create a new class instance.
	 *
	 * @param  Container $container
	 * @return void
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Set the argument being sent through the pipeline.
	 *
	 * @param  mixed  $passable
	 * @return $this
	 */
	public function send($passable)
	{
		$this->passable = &$passable;

		return $this;
	}

	/**
	 * Set the array of pipes.
	 *
	 * @param  array|mixed  $pipes
	 * @return $this
	 */
	public function through($pipes)
	{
		$this->pipes = (array) $pipes;

		return $this;
	}

	/**
	 * Set the method to call on the pipes.
	 *
	 * @param  string  $method
	 * @return $this
	 */
	public function via($method)
	{
		$this->method = $method;

		return $this;
	}

	/**
	 * Run the pipeline with a final destination callback.
	 *
	 * @param  \Closure  $destination
	 * @return mixed
	 */
	public function then(Closure $destination)
	{
		return call_user_func(
			array_reduce(
				array_reverse($this->pipes), [$this, 'getSlice'], $destination
			),
			$this->passable
		);
	}

	/**
	 * Get a Closure that represents a slice of the application onion.
	 *
	 * @return \Closure
	 */
	protected function getSlice($stack, $pipe)
	{
		return function ($passable) use ($stack, $pipe) {
			return call_user_func($this->normalize($pipe), $passable, $stack);
		};
	}
	
	/**
	 * Get callable pipe.
	 * 
	 * @param string|object $pipe 
	 * @return callable
	 * */
	protected function normalize(&$pipe)
	{
		if (is_string($pipe)) {
			return [$this->container->make($pipe), $this->method];
		} elseif (is_object($pipe)) {
			return [$pipe, $this->method];
		}
		return $pipe;
	}

}
