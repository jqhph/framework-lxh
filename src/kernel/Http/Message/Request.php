<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;

/**
 * 代表客户端请求的 HTTP 消息对象。
 *
 * 根据规范，每一个 HTTP 请求都包含以下信息：
 *
 * - HTTP 协议版本号 (Protocol version)
 * - HTTP 请求方法 (HTTP method)
 * - URI
 * - 头信息 (Headers)
 * - 消息内容 (Message body)
 *
 * 在构造 HTTP 请求对象的时候，实现类库 **必须** 从给出的 URI 中去提取 HOST 信息。
 *
 * HTTP 请求是被视为无法修改的，所有能修改状态的方法，都 **必须** 有一套机制，在内部保
 * 持好原有的内容，然后把修改状态后的，新的 HTTP 请求实例返回。
 */
class Request extends Message implements RequestInterface
{
    private $uri;
    private $method;
    private $target;

    public function __construct(array $headers = array(), StreamInterface $body = null)
    {
        $this->method = get_value($_SERVER, 'REQUEST_METHOD');

        parent::__construct($headers, $body);
    }

    /**
     * @return Uri
     */
    protected function initServerUri()
    {
        if ($this->uri) {
            return;
        }

        $this->uri = $this->createUri();
    }

    /**
     * @param null $uri
     * @return Uri
     */
    public function createUri($uri = null)
    {
        if (! $uri) {
            $user = get_value($_SERVER, 'PHP_AUTH_USER');
            $pwd = get_value($_SERVER, 'PHP_AUTH_PW');
            $auth = '';
            if ($user && $pwd) {
                $auth = "{$user}:{$pwd}@";
            }
            $scheme = get_value($_SERVER, 'REQUEST_SCHEME');
            $uri = "{$scheme}://{$auth}{$_SERVER['HTTP_HOST']}:{$_SERVER['SERVER_PORT']}{$_SERVER['REQUEST_URI']}";
        }

        return new Uri($uri);
    }

    /**
     * 获取消息请求的目标。
     *
     * 在大部分情况下，此方法会返回完整的 URI，除非 `withRequestTarget()` 被设置过。
     *
     * 如果没有提供 URI，并且没有提供任何的请求目标，此方法 **必须** 返回 "/"。
     *
     * @return string
     */
    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
        $this->initServerUri();

        if (!empty($this->target)) {
            return $this->target;
        }
        if ($this->uri instanceof UriInterface) {
            $target = $this->uri->getPath();
            if ($target == '') {
                $target = '/';
            }
            if ($this->uri->getQuery() != '') {
                $target .= '?' . $this->uri->getQuery();
            }
        } else {
            $target = '/';
        }
        return $this->target = $target;
    }

    /**
     * 返回一个指定目标的请求实例。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 请求实例，然后返回
     * 一个新的修改过的 HTTP 请求实例。
     *
     * @see 关于请求目标的各种允许的格式，请见 http://tools.ietf.org/html/rfc7230#section-2.7
     *
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
        $this->backupsOriginServer();

        $this->target = $requestTarget;
        return $this;
    }

    /**
     * 获取当前请求使用的 HTTP 方法
     *
     * @return string HTTP 方法字符串
     */
    public function getMethod()
    {
        // TODO: Implement getMethod() method.
        return $this->method;
    }

    /**
     * 返回更改了请求方法的消息实例。
     *
     * 虽然，在大部分情况下，HTTP 请求方法都是使用大写字母来标示的，但是，实现类库 **一定不可**
     * 修改用户传参的大小格式。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 请求实例，然后返回
     * 一个新的修改过的 HTTP 请求实例。
     *
     * @param string $method 大小写敏感的方法名
     * @return self
     * @throws \InvalidArgumentException 当非法的 HTTP 方法名传入时会抛出异常。
     */
    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
        $this->backupsOriginServer();

        $this->method = $method;
        return $this;
    }

    /**
     * 获取 URI 实例。
     *
     * 此方法必须返回 `UriInterface` 的 URI 实例。
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface 返回与当前请求相关 `UriInterface` 类型的 URI 实例。
     */
    public function getUri()
    {
        // TODO: Implement getUri() method.
        $this->initServerUri();

        return $this->uri;
    }

    /**
     * 返回修改了 URI 的消息实例。
     *
     * 当传入的 `URI` 包含有 `HOST` 信息时，**必须** 更新 `HOST` 头信息，如果 `URI`
     * 实例没有附带 `HOST` 信息，任何之前存在的 `HOST` 信息 **必须** 作为候补，应用
     * 更改到返回的消息实例里。
     *
     * 你可以通过传入第二个参数来，来干预方法的处理，当 `$preserveHost` 设置为 `true`
     * 的时候，会保留原来的 `HOST` 信息。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 请求实例，然后返回
     * 一个新的修改过的 HTTP 请求实例。
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri `UriInterface` 类型的 URI 实例
     * @param bool $preserveHost 是否保留原有的 HOST 头信息
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
        $this->backupsOriginServer();

        $this->uri = $uri;
        if (!$preserveHost) {
            $host = $this->uri->getHost();
            if (!empty($host)) {
                if (($port = $this->uri->getPort()) !== null) {
                    $host .= ':' . $port;
                }
                if ($this->getHeader('host')) {
                    $header = $this->getHeader('host');
                } else {
                    $header = 'Host';
                }
                $this->withHeader($header, $host);
            }
        }
        return $this;
    }

    protected function __clone()
    {
        parent::__clone();

        $this->initServerUri();

    }
}
