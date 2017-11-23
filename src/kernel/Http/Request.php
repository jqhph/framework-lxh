<?php

namespace Lxh\Http;

use Lxh\Http\Header;
use Psr\Http\Message\UriInterface;
use Lxh\Http\Message\Uri;
use Lxh\Http\Message\UploadFile;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * PSR7
 */
class Request extends Message\ServerRequest
{
	const METHOD_HEAD 	  = 'HEAD';
	const METHOD_GET 	  = 'GET';
	const METHOD_POST 	  = 'POST';
	const METHOD_PUT 	  = 'PUT';
	const METHOD_PATCH    = 'PATCH';
	const METHOD_DELETE   = 'DELETE';
	const METHOD_OPTIONS  = 'OPTIONS';
	const METHOD_OVERRIDE = '_METHOD';

	/**
	 * HTTP协议版本号
	 *
	 * @var string
	 */
	protected $protocolVersion = '1.1';

	/**
	 * 主域名
	 *
	 * @var string
	 */
	protected $domain;

	public function __construct()
	{
		parent::__construct();

		$this->protocolVersion = str_replace('HTTP/', '', get_value($_SERVER, 'SERVER_PROTOCOL'));

		$this->initUploadFiles();

	}

	/**
	 * 获取浏览器header请求头中的国家语言代码
	 *
	 * @return string
	 */
	public function getCountryCode()
	{
		$current = substr($this->server('HTTP_ACCEPT_LANGUAGE'), 0, 5);

		$current = explode('-', $current);

		return isset($current[1]) ? $current[1] : 'US';
	}

	/**
	 * 获取server参数
	 *
	 * @param string $key
	 * @param $mixed $value
	 * @return string
	 */
	public function server($key, $value = null)
	{
		return get_value($this->serverParams, $key, $value);
	}

	protected function initUploadFiles()
	{
		foreach ($_FILES as $k => & $file) {
			if (empty($file['tmp_name']) || empty($file['name'])) {
				continue;
			}
			$this->uploadedFiles[$k] = new UploadFile(
				$file['tmp_name'],
				(int) $file['size'],
				(int) $file['error'],
				$file['name'],
				$file['type']
			);
		}
	}

	/**
	 * 设置或获取当前包含协议的域名
	 * @access public
	 * @param string $domain 域名
	 * @return string
	 */
	public function primaryDomain($domain = null)
	{
		if (!is_null($domain)) {
			$this->domain = $domain;
			return $this;
		} elseif (!$this->domain) {
			$this->domain = $this->host();
		}
		return $this->domain;
	}

	public function time()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    public function date()
    {
        return date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    }

	/**
	 * 当前请求 SERVER_PROTOCOL
	 * @access public
	 * @return integer
	 */
	public function protocol()
	{
		return $this->server('SERVER_PROTOCOL');
	}

	/**
	 * 当前URL地址中的scheme参数
	 * @access public
	 * @return string
	 */
	public function scheme()
	{
		return $this->isSsl() ? 'https' : 'http';
	}

	/**
	 * 检测是否使用手机访问
	 * @access public
	 * @return bool
	 */
	public function isMobile()
	{
		if (stristr($this->server('HTTP_VIA'), 'wap')) {
			return true;
		} elseif (strpos(strtoupper($this->server('HTTP_ACCEPT')), 'VND.WAP.WML')) {
			return true;
		} elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
			return true;
		} elseif (
			preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i',
			$this->server('HTTP_USER_AGENT')
		)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 是否为cli
	 * @access public
	 * @return bool
	 */
	public function isCli()
	{
		return PHP_SAPI == 'cli';
	}

	/**
	 * 是否为cgi
	 * @access public
	 * @return bool
	 */
	public function isCgi()
	{
		return strpos(PHP_SAPI, 'cgi') === 0;
	}
	
	public function isPOST() 
	{
		return $this->getMethod() === static::METHOD_POST;
	}

	public function isGET()
	{
		return $this->getMethod() === static::METHOD_GET;
	}

	public function isPATCH()
	{
		return $this->getMethod() === static::METHOD_PATCH;
	}

	public function isPUT()
	{
		return $this->getMethod() === static::METHOD_PUT;
	}

	public function isDELETE()
	{
		return $this->getMethod() === static::METHOD_DELETE;
	}

	public function isHead()
	{
		return $this->getMethod() === static::METHOD_HEAD;
	}

	public function isOptions()
	{
		return $this->getMethod() === static::METHOD_OPTIONS;
	}
	
	public function isAjax() 
	{
		return $this->getHeaderLine('X_REQUESTED_WITH') === 'XMLHttpRequest';
	}
	
	public function isSSL()
	{
		if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
			return true;
		} elseif (443 == $this->host()) {
			return true;
		}
		return false;
	}

	/**
	 * 获取客户端IP地址
	 * @param integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @param boolean   $adv 是否进行高级模式获取（有可能被伪装）
	 * @return mixed
	 */
	public function ip($type = 0, $adv = false)
	{
		$type      = $type ? 1 : 0;
		static $ip = null;
		if (null !== $ip) {
			return $ip[$type];
		}

		if ($adv) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$pos = array_search('unknown', $arr);
				if (false !== $pos) {
					unset($arr[$pos]);
				}
				$ip = trim(current($arr));
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
		return $ip[$type];
	}

	/**
	 * 当前请求的host
	 * @access public
	 * @return string
	 */
	public function host()
	{
		return $this->server('HTTP_HOST');
	}

	/**
	 * 当前请求URL地址中的port参数
	 * @access public
	 * @return integer
	 */
	public function port()
	{
		return $this->server('SERVER_PORT');
	}

}
