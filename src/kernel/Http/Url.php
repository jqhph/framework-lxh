<?php

namespace Lxh\Http;

use Lxh\Http\Message\Uri;
use Psr\Http\Message\UriInterface;

class Url
{
    /**
     * @var static
     */
    protected static $current;

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

    public function __construct(Uri $uri = null)
    {
        $this->request = request();
        if ($uri) {
            $this->setUri($uri);
        }
    }

    /**
     * 获取当前url
     *
     * @return Url|static
     */
    public static function current()
    {
        if (static::$current) {
            return static::$current;
        }

        static::$current = new static(request()->getUri());

        return static::$current;
    }

    public function setUri(UriInterface $uri)
    {
        $this->uri = $uri;

        if ($q = $this->uri->getQuery()) {
            parse_str($q, $this->query);
        }
        $this->path = $this->uri->getPath();

        return $this;
    }

    /**
     * @return $this
     */
    public function create()
    {
        if (! $this->uri) {
            $this->setUri($this->request->createUri());
        }

        return $this;
    }

    /**
     * @return Uri
     */
    public function uri()
    {
        if (! $this->uri) $this->create();

        return $this->uri;
    }

    /**
     * 设置url query参数
     *
     * @return static | array
     */
    public function query($k = null, $v = null)
    {
        if (! $this->uri) $this->create();

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
        if (! $this->uri) $this->create();
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
        if (! $this->uri) $this->create();

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
        if (! $this->uri) $this->create();

        return call_user_func_array([$this->uri, $name], $arguments);
    }
}
