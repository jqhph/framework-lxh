<?php

namespace Lxh\Events;

use Lxh\Contracts\Container\Container;
use Lxh\Support\Str;

class Dispatcher implements \Lxh\Contracts\Events\Dispatcher
{
	/**
	 * @var Container
	 */
	protected $container;
	
	/**
	 * The registered event listeners.
	 *
	 * @var array
	 */
	protected $listeners = [];
	
	/**
	 * The wildcard listeners.
	 *
	 * @var array
	 */
	protected $wildcards = [];
	
	/**
	 * The sorted event listeners.
	 *
	 * @var array
	 */
	protected $sorted = [];
	
	/**
	 * The event firing stack.
	 *
	 * @var array
	 */
	protected $firing = [];
	
	/**
	 * The queue resolver instance.
	 *
	 * @var callable
	 */
	protected $queueResolver;
	
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * 监听事件
	 *
	 * @param string $event
	 * @param mixed $listener
	 * @param int $priority
	 */
	public function listen($event, $listener, $priority = 0)
	{
		if (strpos($event, '*') !== false) {
			$this->wildcards[$event][] = $listener;
		} else {
			$this->listeners[$event][$priority][] = $listener;

			unset($this->sorted[$event]);
		}

	}
	
	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  mixed  $listener
	 * @return mixed
	 */
	public function resolveListener($listener)
	{
		if (is_string($listener)) {
			list($class, $method) = $this->parseClassCallable($listener);

			return [$this->container->make($class), $method];
		}

		return $listener;
	}
	
	/**
	 * Parse the class listener into class and method.
	 *
	 * @param  string  $listener
	 * @return array
	 */
	protected function parseClassCallable($listener)
	{
		$segments = explode('@', $listener);
	
		return [$segments[0], !empty($segments[1]) ? $segments[1] : 'handle'];
	}
	
	/**
	 * 触发监听事件
	 * 可以用event函数代替
	 *
	 * @param string|object $event   事件名称或者事件名称的对象
	 * @param mixed			$payload 触发事件传入的参数
	 * @param bool			$halt	 是否在监听事件return后终止执行监听事件
	 * @return mixed
	 * */
	public function fire($event, $payload = [], $halt = false)
	{
		// When the given "event" is actually an object we will assume it is an event
		// object and use the class as the event name and this event itself as the
		// payload to the handler, which makes object based events quite simple.
		if (is_object($event)) {
			$payload = [$event];
			$event   = get_class($event);
		}
		
		$responses = [];
		
		// If an array is not given to us as the payload, we will turn it into one so
		// we can easily use call_user_func_array on the listeners, passing in the
		// payload to each of them so that they receive each of these arguments.
		if (! is_array($payload)) {
			$payload = [$payload];
		}
		
		foreach ($this->getListeners($event) as & $listener) {
			$response = call_user_func_array($this->resolveListener($listener), $payload);
		
			// If a response is returned from the listener and event halting is enabled
			// we will just return this response, and not call the rest of the event
			// listeners. Otherwise we will add the response on the response list.
			if (! is_null($response) && $halt) {
				//array_pop($this->firing);
		
				return $response;
			}
		
			// If a boolean false is returned from a listener, we will stop propagating
			// the event to any further listeners down in the chain, else we keep on
			// looping through the listeners and firing every one in our sequence.
			if ($response === false) {
				break;
			}
		
			$responses[] = $response;
		}
		
		return $halt ? null : $responses;
		
	}
	
	/**
	 * Get all of the listeners for a given event name.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	public function getListeners($eventName)
	{
		$wildcards = $this->getWildcardListeners($eventName);
	
		if (! isset($this->sorted[$eventName])) {
			$this->sortListeners($eventName);
		}
	
		return array_merge($this->sorted[$eventName], $wildcards);
	}
	
	/**
	 * Sort the listeners for a given event by priority.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function sortListeners($eventName)
	{
		$this->sorted[$eventName] = [];
	
		// If listeners exist for the given event, we will sort them by the priority
		// so that we can call them in the correct order. We will cache off these
		// sorted event listeners so we do not have to re-sort on every events.
		if (isset($this->listeners[$eventName])) {
			krsort($this->listeners[$eventName]);
	
			$this->sorted[$eventName] = call_user_func_array(
				'array_merge', $this->listeners[$eventName]
			);
		}
	}
	
	/**
	 * Get the wildcard listeners for the event.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function getWildcardListeners($eventName)
	{
		$wildcards = [];
	
		foreach ($this->wildcards as $key => & $listeners) {
			if (Str::is($key, $eventName)) {
				$wildcards = array_merge($wildcards, $listeners);
			}
		}
	
		return $wildcards;
	}
	
	/**
	 * Fire an event until the first non-null response is returned.
	 *
	 * @param  string|object  $event
	 * @param  array  $payload
	 * @return mixed
	 */
	public function until($event, $payload = [])
	{
		return $this->fire($event, $payload, true);
	}
	
	/**
	 * Register an event subscriber with the dispatcher.
	 *
	 * @param  object|string  $subscriber
	 * @return void
	 */
	public function subscribe($subscriber)
	{
		$subscriber = $this->resolveSubscriber($subscriber);
	
		$subscriber->subscribe($this);
	}
	
	/**
	 * Resolve the subscriber instance.
	 *
	 * @param  object|string  $subscriber
	 * @return mixed
	 */
	protected function resolveSubscriber($subscriber)
	{
		if (is_string($subscriber)) {
			return $this->container->make($subscriber);
		}
	
		return $subscriber;
	}
	
	public function flush($event)
	{
		$this->fire($event.'_pushed');
	}
	
	/**
	 * Remove a set of listeners from the dispatcher.
	 *
	 * @param  string  $event
	 * @return void
	 */
	public function forget($event)
	{
		if (strpos($event, '*') !== false) {
			unset($this->wildcards[$event]);
		} else {
			unset($this->listeners[$event], $this->sorted[$event]);
		}
	}
	
	/**
	 * Forget all of the pushed listeners.
	 *
	 * @return void
	 */
	public function forgetPushed()
	{
		foreach ($this->listeners as $key => $value) {
			if (strrpos($key, '_pushed') !== false) {
				$this->forget($key);
			}
		}
	}
	
	public function push($event, $payload = [])
	{
		$this->listen($event.'_pushed', function () use ($event, $payload) {
			$this->fire($event, $payload);
		});
	}
	
	public function hasListeners($eventName)
	{
		return isset($this->listeners[$eventName]) || isset($this->wildcards[$eventName]);
	}

}
