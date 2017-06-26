<?php

namespace Lxh\Http;

class Header 
{
	/**
	 * Special-case HTTP headers that are otherwise unidentifiable as HTTP headers.
	 * Typically, HTTP headers in the $_SERVER array will be prefixed with
	 * `HTTP_` or `X_`. These are not so we list them here for later reference.
	 *
	 * @var array
	 */
	protected static $special = [
		'CONTENT_TYPE',
		'CONTENT_LENGTH',
		'PHP_AUTH_USER',
		'PHP_AUTH_PW',
		'PHP_AUTH_DIGEST',
		'AUTH_TYPE',
		'HTTP_ESPO_CGI_AUTH',
		'HTTP_JQH_AUTHORIZATION',
		'HTTP_AUTHORIZATION'			
	];
	
	protected $headers = [];
	
	/**
	 * 储存要回复给客户端的header头信息
	 *
	 * @var array
	 */
	protected $data = [];
	
	protected $defaultData = [];
	
	public function __construct() 
	{

	}
	
	# 获取客户端请求header头信息
	public function extract($name = null) 
	{
		if (! $this->headers) {
	        foreach ($_SERVER as $key => & $value) {
	            $key = strtoupper($key);
	            if (strpos($key, 'X_') === 0 || strpos($key, 'HTTP_') === 0 || in_array($key, static::$special)) {
// 	                if ($key === 'HTTP_CONTENT_LENGTH') {
// 	                    continue;
// 	                }
	                $this->headers[$key] = $value;
	            }
	        }
		}
	
		if ($name) {
			return isset($this->headers[$name]) ? $this->headers[$name] : null;
		}
        return $this->headers;
    }

	/**
	 * 当前请求 SERVER_PROTOCOL
	 * @access public
	 * @return integer
	 */
	public function protocol()
	{
		return $_SERVER['SERVER_PROTOCOL'];
	}
    
    /**
     * 设置header头信息
     * 
     * 示例：
	 *  $this->set('Content-type', 'text/html; charset=utf-8')
	 * 最后返回：
	 *  header('Content-type: text/html; charset=utf-8');
     * 
     * @param string|array $name
     * @param string $value
     * @return void  
     */
    public function set($name, $value = null) {
    	if (is_array($name)) {
    		foreach ($name as $k => & $v) {
    			if (! $k) {
    				continue;
    			}
//    			$this->normalizeKey($k);
    			$this->data[$k] = $v;
    		}
    		return true;
    	}
//    	$this->normalizeKey($name);
    	$this->data[$name] = $value;
    }
    
    /**
     * 获取header头数组
     * */
    public function get() 
    {
    	return $this->data;
    }
    
    protected function normalizeKey(& $key) 
    {
//    	$key = strtolower($key);
//    	$key = str_replace(['-', '_'], ' ', $key);
//    	$key = preg_replace('#^http #', '', $key);
//    	$key = ucwords($key);
//    	$key = str_replace(' ', '-', $key);
    }
	
}
