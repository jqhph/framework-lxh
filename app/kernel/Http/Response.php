<?php

namespace Lxh\Http;

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
	 * @var mixed
	 */
	public $data;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var int Length of HTTP response body
	 */
	protected $length;

	/**
	 * @var View
	 */
	public $viewManager;

	public function __construct(Request $request, Container $container)
	{
		$this->container = $container;

		$this->request = $request;

		$this->contentType($this->contentType, $this->charset);

	}

	/**
	 * 获取模板管理器
	 *
	 * @return View
	 */
	public function view()
	{
		return $this->viewManager ?: ($this->viewManager = $this->container->make('view'));
	}
	
	//发送header头及返回消息
	public function sendHeader() 
	{
		//Send status
		if (strpos(PHP_SAPI, 'cgi') === 0) {
			header(sprintf('Status: %s', $this->getReasonPhrase()));

		} else {
			header(sprintf('%s %s, %s', $this->request->protocol(), $this->getStatusCode(), $this->getReasonPhrase()));

		}

		foreach ($this->getHeaders() as $name => & $value) {
			header($name . ': ' . implode('; ', $value), false);
		}
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
	 * Expires
	 * @param string $time
	 * @return static
	 */
	public function expires($time)
	{
		$this->heade->set('Expires', $time);
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
	 * @param string $charset     输出编码
	 * @return static
	 */
	public function contentType($contentType, $charset = 'utf-8')
	{
		$this->withHeader('Content-Type', $contentType . '; charset=' . $charset);
		return $this;
	}
	
	/**
	 * 输出内容到客户端
	 *
	 * @return void
	 */
	public function send()
	{
	    $this->container->make('events')->fire('response.send.before', [$this->request, $this]);

		$this->sendHeader();

		if (is_array($this->data)) {
			echo json_encode($this->data);
		} else {
			echo $this->data;
		}

        $this->container->make('events')->fire('response.send.after', [$this->request, $this]);

		$controllerManager = $this->container->make('controller.manager');

		// 非生产环境和非命令行环境则输出控制台调试日志
		if (! is_prod() && ! $this->request->isCli() && config('response-console-log', true) && ! $this->request->isAjax() && $controllerManager->controllerName() != 'Js') {
			echo Console::fetch();
		}
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
		$this->data = & $data;
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
	 */
	public function redirect($url, $status = 302) 
	{
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
