<?php

namespace Lxh\Logger;

use Lxh\Helper\Arr;
use Monolog\Handler\HandlerInterface;
use Lxh\Basis\Factory;
use Monolog\Logger;
use Lxh\Contracts\Container\Container;

/**
 * 日志处理 
 * */
class Manager extends Factory
{
	protected $channelName;

	protected $config;

	protected $defaultExceptionConfig = [
			'channel' => 'exception',
			'path'    => 'data/logs/exception/record.log',
			'handlers' => [
				[
					'handler' 	=> 'DaysFileHandler',
					'formatter' => 'TextFormatter',
					'level' 	=> '100'
				]
			],
			'maxFiles' => 180,
			'filenameDateFormat' => 'Y-m-d'
		];

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	# create a log channel 		setFormatter(FormatterInterface $formatter)
	public function create($name)
	{
		$logger = new Logger($name);

		$config = $this->getLoggerConfig();

		if (! isset($config[$name])) {
			return $logger;
		}

		$this->pushHandlers($logger, (array) $config[$name]);

		return $logger;
	}

	/**
	 * 获取一个日志处理通道（对象）
	 * */
	public function channel($channelName)
	{
		$this->get($channelName);//获取一个channel
		$this->channelName = $channelName;
		return $this;
	}

	/**
	 * 给logger channel添加handler
	 * */
	protected function pushHandlers(Logger $channel, array $config)
	{
		$defaultConfig = & $this->defaultExceptionConfig;
		//日志路径
		$path				= Arr::getValue($config, 'path', $defaultConfig['path']);
		//日志handler处理器信息
		$handlers		    = Arr::getValue($config, 'handlers', $defaultConfig['handlers']);
		//目录下最大文件数
		$maxFiles			= Arr::getValue($config, 'maxFiles', 180);
		//日期格式化
		$filenameDateFormat = Arr::getValue($config, 'filenameDateFormat', 'Y-m-d');
		
		if (! $maxFiles) {
			$maxFiles = 0;
		}
		
		if (count($handlers) < 1) {
			$handlers = & $defaultConfig['handlers'];
		}
		
		foreach ($handlers as & $info) {//添加日志处理器
			if (! $info || count($info) < 1) {
				continue;
			}
		
			$handler = Arr::getValue($info, 'handler', $defaultConfig['handlers'][0]['handler']);
				
			$handelClass = 'Lxh\\Logger\\Handler\\' . $handler;
			if (! class_exists($handelClass)) {
				$handelClass = '\\Monolog\\Handler\\' . $handler;
			}
		
			$lowestLevel = Arr::getValue($info, 'level', \Monolog\Logger::DEBUG);
			$bubble      = Arr::getValue($info, 'bubble', true);
			$path		 = Arr::getValue($info, 'path', $path);

			$handler = new $handelClass($path, $lowestLevel, $bubble);//实例化日志处理器
				
			if (($maxFiles || $maxFiles === 0) && method_exists($handler, 'setMaxFiles')) {
				$handler->setMaxFiles($maxFiles);
			}
				
			if ($filenameDateFormat && method_exists($handler, 'setDateFormat')) {
				$handler->setDateFormat($filenameDateFormat);
			}
		
			if (! empty($info['formatter'])) {
				$fomatterClass = 'Lxh\\Logger\\Formatter\\' . $info['formatter'];
				if (! class_exists($fomatterClass)) {
					$fomatterClass = '\\Monolog\\Formatter\\' . $info['formatter'];
				}
					
				$handler->setFormatter(new $fomatterClass());
			}
		
			$channel->pushHandler($handler);//添加handler
		}
	}

	/**
	 * 代理monolog处理日志方法, 使用前请先通过配置文件定义好相应的日志通道信息
	 * 使用示例:
	 *  logger()->info('啦啦~'); //此方法会在日志信息后面带上$_SERVER['REQUEST_METHOD']和$_SERVER['REQUEST_URI']等信息, info表示日志级别
	 * 输出如下:
	 *  [2016-09-18 18:12:58] INFO: 啦啦~ [GET: /api/Index/index] [] []
	 *
	 *  logger()->addInfo('啦啦~');//此方法推送原始日志信息, info表示日志级别
	 * 输出如下:
	 *  [2016-09-18 18:13:16] INFO: 啦啦~ [] []
	 *
	 *
	 * DEBUG (100): 详细的debug信息。
	 * INFO (200): 关键事件。
	 * NOTICE (250): 普通但是重要的事件。
	 * WARNING (300): 出现非错误的异常。
	 * ERROR (400): 运行时错误，但是不需要立刻处理。
	 * CRITICA (500): 严重错误。
	 * EMERGENCY (600): 系统不可用。
	 */
	public function error($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->error($msg, $extra);
	}

	public function addError($msg, $extra = [])
	{
		return $this->get($this->channelName)->error($msg, $extra);
	}

	public function warning($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->warning($msg, $extra);
	}

	public function addWarning($msg, $extra = [])
	{
		return $this->get($this->channelName)->warning($msg, $extra);
	}

	public function notice($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->warning($msg, $extra);
	}

	public function addNotice($msg, $extra = [])
	{
		return $this->get($this->channelName)->warning($msg, $extra);
	}

	public function info($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->info($msg, $extra);
	}

	public function addInfo($msg, $extra = [])
	{
		return $this->get($this->channelName)->info($msg, $extra);
	}

	public function critica($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->critica($msg, $extra);
	}

	public function addCritica($msg, $extra = [])
	{
		return $this->get($this->channelName)->critica($msg, $extra);
	}

	public function emergency($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->emergency($msg, $extra);
	}

	public function addEmergency($msg, $extra = [])
	{
		return $this->get($this->channelName)->emergency($msg, $extra);
	}

	public function alert($msg, $extra = [])
	{
		$extra[$_SERVER['REQUEST_METHOD']] = $_SERVER['REQUEST_URI'];
		return $this->get($this->channelName)->alert($msg, $extra);
	}

	public function addAlert($msg, $extra = [])
	{
		return $this->get($this->channelName)->alert($msg, $extra);
	}

	protected function getLoggerConfig()
	{
		if (! $this->config) {
			$this->config = config('logger');
		}
		return $this->config;
	}
	
}
