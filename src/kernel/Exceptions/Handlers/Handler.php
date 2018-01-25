<?php

namespace Lxh\Exceptions\Handlers;

use Lxh\Contracts\Container\Container;
use Lxh\Debug\Code;
use Lxh\Events\Dispatcher;
use Lxh\Exceptions\Exception;
use Lxh\Exceptions\Forbidden;
use Lxh\Exceptions\NotFound;
use Lxh\Logger\Manager;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Http\Message\Status;
use Lxh\ORM\Connect\PDO;

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

	/**
	 * @var Dispatcher
	 */
	protected $events;

	protected $exceptionClasses = [
		'PDOException' => 'pdo',
		'RedisException' => 'redis',
		Forbidden::class => 'forbidden',
		NotFound::class => 'notFound',

		'Lxh\Exceptions\Exception' => 'system',
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

	public function __construct(Manager $logger, Request $req, Response $resp, Dispatcher $events)
	{
		$this->logger = $logger;
		$this->request = $req;
		$this->response = $resp;
		$this->events = $events;
	}

	/**
	 * 数据库相关异常
	 */
	public function pdo(\Exception $e)
	{
		$lastSql = &PDO::$lastSql;

		$trace = $e->getTrace();

		$this->logger
			->channel('pdo')
			->error(
				"{$e->getMessage()} [line({$e->getLine()})] [{$lastSql}]",
				['params' => & PDO::$lastPrepareData, 'trace' => array_splice($trace, 1, 6)]
			);

		$this->responseError($e);
	}

	public function redis(\Exception $e)
	{

	}

	protected function getLoggerMethod($level)
	{
		$method = 'error';
		if (isset($this->levels[$level])) {
			$method = $this->levels[$level];
		}
		return $method;
	}

	/**
	 * 普通异常处理
	 */
	public function normal(\Exception $e)
	{
		$trace = $e->getTrace();

		$this->logger
			->channel('exception')
			->error($this->normalizeExceptionReportString($e), array_splice($trace, 0, 4));

		//返回错误提示
		$this->responseError($e);
	}

	/**
	 * 系统自定义异常处理
	 *
	 */
	public function system(\Exception $e, $level = false)
	{
		$recordMethod = $this->getLoggerMethod($e->getLevel());

		$trace = $e->getTrace();

		$this->logger
			->channel('exception')
			->$recordMethod($this->normalizeExceptionReportString($e), array_splice($trace, 0, 4));

		// 返回错误提示
		$this->responseError($e);
	}

	protected function normalizeExceptionReportString(\Exception $e)
	{
		$class = get_class($e);

		return "Exception '$class' with message '{$e->getMessage()}' in {$e->getFile()}:{$e->getLine()}";
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
	 * 无权访问
	 *
	 * @return void
	 */
	protected function forbidden(Forbidden $e)
	{
		$this->response->withStatus($e->getCode());
		if (is_ajax()) {
			$this->response->data($e->getMessage());
			return;
		}

		$this->response->data(
			view('admin::error.403', ['msg' => $e->getMessage()])->render()
		);
	}

	protected function notFound(NotFound $e)
	{
		$this->response->withStatus($e->getCode());
		if (is_ajax()) {
			$this->response->data($e->getMessage());
			return;
		}

		$this->response->data(
			view('admin::error.404', ['msg' => $e->getMessage()])->render()
		);
	}

	/**
	 * 返回HTTP状态码及错误信息
	 */
	protected function responseError(\Exception $e)
	{
		$this->events->fire(EVENT_EXCEPTION_REPORT, [$e]);

		$code = $e->getCode();

		if ($code) {
			if (! Status::vertify($code)) {
				$code = 500;
			}

			$this->response->withStatus($code);
		}

		$isAjax = $this->request->isAjax();

		$vars = [
			'msg' => $e->getMessage(),
			'code' => $e->getCode(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTraceAsString(),
			'preview' => Code::source($e->getFile(), $e->getLine(), 8)
		];

		// 非生产环境以及非ajax请求显示错误界面
		if (! is_prod() && ! $isAjax && ! $this->request->isCli()) {
			return $this->response->data = resolve('view.adaptor')->get()->make('system.debug', $vars)->render();
		}

		if (! is_prod() && $isAjax || $this->request->isCli()) {
			unset($vars['preview']);
			return $this->response->data = $vars;
		}
	}

}
