<?php

namespace Lxh\Http;

use Lxh\Http\Message\Uri;

class Url
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;

    protected $query = [];

    protected $path;

    public function __construct()
    {
        $this->request = request();
        $this->uri = $this->request->getUri();

        if ($q = $this->uri->getQuery()) {
            parse_str($q, $this->query);
        }
        $this->path = $this->uri->getPath();
    }

    /**
     * @return Uri
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * 设置url query参数
     *
     * @return static | array
     */
    public function query($k = null, $v = null)
    {
        if (! $k === null) {
            return $this->query;
        }
        if (is_array($k)) {
            $this->query = array_merge($this->query, $k);
        } else {
            $this->query[$k] = $v;
        }
        return $this;
    }

    /**
     * 移除query参数
     *
     * @param string | array $keys
     * @return static
     */
    public function unsetQuery($keys)
    {
        foreach ((array) $keys as &$k) {
            unset($this->query[$k]);
        }
        return $this;
    }

    /**
     * 设置路径
     *
     * @return static | string
     */
    public function path($path = null)
    {
        if ($path != null) {
            $this->path = $path;
            return $this;
        }
        return $this->path;
    }

    /**
     * 获取url字符串
     *
     * @return string
     */
    public function string()
    {
        $this->uri->withPath($this->path);
        $this->uri->withQuery(http_build_query($this->query));

        return (string)$this->uri;
    }
    
    public function __toString()
    {
        return $this->string();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->uri, $name], $arguments);
    }
}
