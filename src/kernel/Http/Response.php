<?php

namespace Lxh\Http;

use Lxh\Admin\Grid;
use Lxh\Http\Header;
use Lxh\Http\Request;
use Lxh\Contracts\Container\Container;
use Lxh\Template\View;
use Lxh\Helper\Entity;
use Lxh\Http\Message\Response as PsrResponse;
use Lxh\Http\Message\Status;
use Lxh\Helper\Console;

/**
 * PSR7
 */
class Response extends PsrResponse
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * 默认输出内容类型
	 *
	 * @var string
	 */
	protected $contentType = 'text/html';

	/**
	 * 默认输出内容字符集
	 *
	 * @var string
	 */
	protected $charset = 'utf-8';

	/**
	 * 要输出的内容
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var int Length of HTTP response body
	 */
	protected $length;

	/**
	 * 是否输出控制器信息
	 *
	 * @var bool
	 */
	protected $outputConsoleLog = true;

	/**
	 * @var bool
	 */
	protected $sent = false;

	/**
	 * @var bool
	 */
	protected $headerSent = false;

	protected $events;

	public function __construct(Request $request, Container $container)
	{
		$this->container = $container;

		$this->events = events();

		$this->request = $request;

		$this->contentType($this->contentType, $this->charset);

	}

	// 发送header头及返回消息
	protected function sendHeader()
	{
		if ($this->request->isCli() || $this->headerSent) return;

		//Send status
		header(sprintf('%s %s, %s', $this->request->protocol(), $this->getStatusCode(), $this->getReasonPhrase()));

		foreach ($this->headers as $name => &$value) {
			header($name . ': ' . implode('; ', $value), false);
		}

		$this->headerSent = true;
	}

	/**
	 * LastModified
	 * @param string $time
	 * @return static
	 */
	public function lastModified($time)
	{
		$this->withHeader('Last-Modified', $time);
		return $this;
	}

	/**
	 * WWW-Authenticate鉴权方法
	 * 传入账号密码验证请求是否合法
	 * 合法返回true，不合法返回false
	 * 如不合法会返回401状态码给客户端，并提示客户端输入账号密码
	 *
	 * @param string $user
	 * @param string $pwd
	 * @return bool
	 */
	public function authenticate($user, $pwd)
	{
		// 安全验证
		if ("$user:$pwd" == $this->request->getUri()->getUserInfo()) {
			return true;
		}

		$this->withHeader('WWW-Authenticate', 'Basic realm=""');
		$this->withStatus(401);
		return false;
	}

	/**
	 * Expires
	 * @param string $time
	 * @return static
	 */
	public function expires($time)
	{
		$this->withHeader('Expires', $time);
		return $this;
	}

	/**
	 * ETag
	 * @param string $eTag
	 * @return static
	 */
	public function eTag($eTag)
	{
		$this->withHeader('ETag', $eTag);
		return $this;
	}

	/**
	 * 页面缓存控制
	 * @param string $cache 状态码
	 * @return static
	 */
	public function cacheControl($cache)
	{
		$this->withHeader('Cache-control', $cache);
		return $this;
	}

	/**
	 * 页面输出类型
	 * @param string $contentType 输出类型
	 * @param string $charset 输出编码
	 * @return static
	 */
	public function contentType($contentType, $charset = 'utf-8')
	{
		$this->withHeader('Content-Type', $contentType . '; charset=' . $charset);
		return $this;
	}

	/**
	 * 刷新当前界面
	 *
	 * @param string | array $params url参数
	 * @return void
	 */
	public function reload($params = '')
	{
		$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';

		if (is_array($params)) {
			$params = http_build_query($params);
		}

		if ($params) {
			if (strpos($url, '?') !== false) {
				$url .= "&$params";
			} else {
				$url .= "?$params";
			}
		}

		$this->withHeader('Location', $url);
	}

	/**
	 * 判断是否已经输出过数据到浏览器
	 *
	 * @return bool
	 */
	public function sent()
	{
		return $this->sent;
	}

	/**
	 * 输出内容到客户端
	 *
	 * @return void
	 */
	public function send()
	{
		if ($this->sent) {
			return;
		}
        $this->container->tracer->profileEnd('framework');

		$this->events->fire(EVENT_RESPONSE_BEFORE);

		$this->sendHeader();

		if (($data = $this->normalizeContent()) !== false) {
			$this->sent = true;

			echo ob_get_clean();

			echo $data;
		}

		$this->events->fire(EVENT_RESPONSE_AFTER);

		$this->sendConsole();
		$this->reportError();
	}

	protected function reportError()
	{
		if (!($error = error_get_last())) {
			return;
		}
		if (in_array((int)$error['type'], config('record-error-info-level'), true)) {
			$this->container['errorHandler']->handle($error);
		}
	}

	/**
	 * 打印数据到客户端
	 *
	 * @return bool 成功返回true，失败返回false
	 */
	protected function &normalizeContent()
	{
		try {
			$string = '';
			foreach ((array)$this->data as &$data) {
				if (is_array($data)) {
					$json = json_encode($data);
					if ($json === false) {
						throw new \InvalidArgumentException(json_last_error_msg());
					}

					$string .= $json;
				} else {
					$string .= $data;
				}
			}

			return $string;
		} catch (\Exception $e) {
			$this->events->fire(EVENT_EXCEPTION, [$e], true);
			$string = false;

			return $string;
		}
	}

	public function clear()
	{
		$this->data = '';
	}

	protected function sendConsole()
	{
		$isProd = is_prod();

		if (! $isProd && $this->outputConsoleLog) {
			$this->container->tracer->handle();
		}

		// 非生产环境和非命令行环境则输出控制台调试日志
		if (
			$this->outputConsoleLog && !is_prod()
			&& !$this->request->isCli() && config('response-console-log', true)
			&& (!$this->request->isAjax() || ($this->request->isAjax() && (Grid::isPjaxRequest() || isset($_GET['_log']))))
		) {
			echo Console::fetch();
		}
	}

	/**
	 * 是否输出控制台调试信息
	 *
	 * @param  bool $flag true输出，false不输出
	 * @return static
	 */
	public function withConsoleOutput($flag = true)
	{
		$this->outputConsoleLog = $flag;
		return $this;
	}

	/**
	 * 获取输出内容
	 *
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->data;
	}

	/**
	 * 输出数据设置
	 * @access public
	 * @param mixed $data 输出数据
	 * @return static
	 */
	public function data($data)
	{
		$this->data = &$data;
		return $this;
	}

	/**
	 * 追加要输出的内容
	 *
	 * @return $this
	 */
	public function append($data)
	{
		$this->data[] = &$data;

		return $this;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function prepend($data)
	{
		array_unshift($this->data, $data);

		return $this;
	}

	/**
	 * Redirect
	 *
	 * This method prepares this response to return an HTTP Redirect response
	 * to the HTTP client.
	 *
	 * @param string $url    The redirect destination
	 * @param int    $status The redirect HTTP status code
	 * @param string | array $params query params
	 * @return void
	 */
	public function redirect($url, $status = 302, $params = '')
	{
		if (is_array($params)) {
			$params = http_build_query($params);
		}

		if ($params) {
			if (strpos($url, '?') !== false) {
				$url .= "&$params";
			} else {
				$url .= "?$params";
			}
		}

		$this->withStatus($status);
		$this->withHeader('Location', $url);
	}

	/**
	 * Helpers: Empty?
	 * @return bool
	 */
	public function isEmpty()
	{
		return in_array($this->getStatusCode(), [201, 204, 304]);
	}

	/**
	 * Helpers: Informational?
	 * @return bool
	 */
	public function isInformational()
	{
		return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
	}

	/**
	 * Helpers: OK?
	 * @return bool
	 */
	public function isOk()
	{
		return $this->getStatusCode() === 200;
	}

	/**
	 * Helpers: Successful?
	 * @return bool
	 */
	public function isSuccessful()
	{
		return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
	}

	/**
	 * Helpers: Redirect?
	 * @return bool
	 */
	public function isRedirect()
	{
		return in_array($this->getStatusCode(), array(301, 302, 303, 307));
	}

	/**
	 * Helpers: Redirection?
	 * @return bool
	 */
	public function isRedirection()
	{
		return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
	}

	/**
	 * Helpers: Forbidden?
	 * @return bool
	 */
	public function isForbidden()
	{
		return $this->getStatusCode() === 403;
	}

	/**
	 * Helpers: Not Found?
	 * @return bool
	 */
	public function isNotFound()
	{
		return $this->getStatusCode() === 404;
	}

	/**
	 * Helpers: Client error?
	 * @return bool
	 */
	public function isClientError()
	{
		return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
	}

	/**
	 * Helpers: Server Error?
	 * @return bool
	 */
	public function isServerError()
	{
		return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
	}

}
