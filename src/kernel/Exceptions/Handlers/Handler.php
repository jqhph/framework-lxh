<?php

namespace Lxh\Exceptions\Handlers;

use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\Exception;
use Lxh\Logger\Manager;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Http\Message\Status;

/**
 * 异常处理
 */
class Handler
{
	/**
	 * @var Manager
	 */
	protected $logger;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var Request
	 */
	protected $request;

	protected $exceptionClasses = [
		'PDOException' => 'db',
		'RedisException' => 'db',
		'Lxh\Exceptions\Exception' => 'system'
	];

	protected $levels = [
		100 => 'debug',
		200 => 'info',
		250 => 'notice',
		550 => 'alert',
		400 => 'error',
		300 => 'warning',
		500 => 'critica',
		600 => 'emergency',
	];

	public function __construct(Container $container)
	{
		$this->logger = $container->make('logger');

		$this->request = $container->make('http.request');

		$this->response = $container->make('http.response');

	}

	/**
	 * 数据库相关异常
	 */
	public function db($e)
	{
		//获取日志处理配置信息
		$this->logger
			->channel('exception')
			->addError(
				$e->getMessage() . $this->getTextSuffix($e->getFile(), $e->getLine())
			);

		$this->responseError($e);
	}

	protected function getLoggerMethod($level)
	{
		$addRecordMethod = 'addError';
		if (isset($this->levels[$level])) {
			$addRecordMethod = 'add' . $this->levels[$level];
		}
		return $addRecordMethod;
	}

	/**
	 * 普通异常处理
	 */
	public function normal($e)
	{
		if ($e->getMessage()) {
			$this->logger
				->channel('exception')
				->addError(
					$e->getMessage() . $this->getTextSuffix($e->getFile(), $e->getLine())
				);
		}

		//返回错误提示
		$this->responseError($e);
	}

	protected function getTextSuffix($file, $line)
	{
		return " [{$_SERVER['REQUEST_METHOD']}, {$_SERVER['REQUEST_URI']}, $file($line)]";
	}


	/**
	 * 系统自定义异常处理
	 *
	 */
	public function system($e, $level = false)
	{
		$recordMethod = $this->getLoggerMethod($e->getLevel());

		if ($e->getMessage()) {
			$this->logger
				->channel('exception')
				->$recordMethod(
					$e->getMessage() . $this->getTextSuffix($e->getFile(), $e->getLine())
				);
		}

		// 返回错误提示
		$this->responseError($e);
	}

	/**
	 * 异常处理入口方法
	 * 用户如需自定义异常处理方法, 请监听"exception.handler"事件
	 *
	 * @param Exception $e
	 * @return void
	 */
	public function handle($e)
	{
		foreach ($this->exceptionClasses as $class => & $way) {
			if ($e instanceof $class) {
				return $this->$way($e);
			}
		}
		return $this->normal($e);
	}

	/**
	 * 返回HTTP状态码及错误信息
	 */
	protected function responseError(\Exception $e)
	{
		$code = $e->getCode();

		$isAjax = $this->request->isAjax();

		if ($code) {
			if (! Status::vertify($code)) {
				$code = 500;

			}

			$this->response->withStatus($code);
		}

		// 非生产环境以及非ajax请求显示错误界面
		if (! is_prod() && ! $isAjax && ! $this->request->isCli()) {
			return $this->response->data = view(
				'system.debug',
				[
					'msg' => $e->getMessage(),
					'code' => $code,
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'trace' => $e->getTraceAsString()
				],
				true
			);
		}

		if (! is_prod() && $isAjax || $this->request->isCli()) {
			return $this->response->data = $e->getMessage();
		}

		// 生产环境
		make('events')->fire('exception.report', ['e' => $e]);
	}

}
