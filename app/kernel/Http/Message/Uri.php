<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * URI 数据对象。
 *
 * 此接口按照 RFC 3986 来构建 HTTP URI，提供了一些通用的操作，你可以自由的对此接口
 * 进行扩展。你可以使用此 URI 接口来做 HTTP 相关的操作，也可以使用此接口做任何 URI
 * 相关的操作。
 *
 * 此接口的实例化对象被视为无法修改的，所有能修改状态的方法，都 **必须** 有一套机制，在内部保
 * 持好原有的内容，然后把修改状态后的，新的实例返回。
 *
 * @see http://tools.ietf.org/html/rfc3986 (URI 通用标准规范)
 */
class Uri implements UriInterface
{
    private $url;
    private $host;
    private $userInfo;
    private $port = 80;
    private $path;
    private $query;
    private $fragment;
    private $scheme;

    public function __construct($url = '')
    {
        if (! $url) {
            return;
        }
        $this->url = $url;
        $parts = parse_url($url);
        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
        $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
        $this->host = isset($parts['host']) ? $parts['host'] : '';
        $this->port = isset($parts['port']) ? $parts['port'] : 80;
        $this->path = isset($parts['path']) ? $parts['path'] : '';
        $this->query = isset($parts['query']) ? $parts['query'] : '';
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }

    /**
     * 获取完整的用户请求url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 从 URI 中取出 scheme。
     *
     * 如果不存在 Scheme，此方法 **必须** 返回空字符串。
     *
     * 返回的数据 **必须** 是小写字母，遵照  RFC 3986 规范 3.1 章节。
     *
     * 最后部分的 ":" 字串不属于 Scheme，**一定不可** 作为返回数据的一部分。
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string URI scheme 的值
     */
    public function getScheme()
    {
        // TODO: Implement getScheme() method.
        return $this->scheme;
    }

    /**
     * 返回 URI 授权信息。
     *
     * 如果没有 URI 信息的话，**必须** 返回一个空数组。
     *
     * URI 的授权信息语法是：
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * 如果端口部分没有设置，或者端口不是标准端口，**一定不可** 包含在返回值内。
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string URI 授权信息，格式为："[user-info@]host[:port]"
     */
    public function getAuthority()
    {
        // TODO: Implement getAuthority() method.
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    /**
     * 从 URI 中获取用户信息。
     *
     * 如果不存在用户信息，此方法 **必须** 返回一个空字符串。
     *
     * 用户信息后面跟着的 "@" 字符，不是用户信息里面的一部分，**一定不可** 在返回值里
     * 出现。
     *
     * @return string URI 的用户信息，格式："username[:password]"
     */
    public function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
        return $this->userInfo;
    }

    /**
     * 从 URI 信息中获取 HOST 值。
     *
     * 如果 URI 中没有此值，**必须** 返回空字符串。
     *
     * 返回的数据 **必须** 是小写字母，遵照  RFC 3986 规范 3.2.2 章节。
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string URI 信息中的 HOST 值。
     */
    public function getHost()
    {
        // TODO: Implement getHost() method.
        return $this->host;
    }

    /**
     * 从 URI 信息中获取端口信息。
     *
     * 如果端口信息是与当前 Scheme 的标准端口不匹配的话，就使用整数值的格式返回，如果是一
     * 样的话，**必须** 返回 `null` 值。
     *
     * 如果存在端口信息，都是不存在 scheme 信息的话，**必须** 返回 `null` 值。
     *
     * 如果不存在端口数据，但是 scheme 数据存在的话，**可以** 返回 scheme 对应的
     * 标准端口，但是 **应该** 返回 `null`。
     *
     * @return null|int 从 URI 信息中的端口信息。
     */
    public function getPort()
    {
        // TODO: Implement getPort() method.
        return $this->port;
    }

    public function getPath()
    {
        // TODO: Implement getPath() method.
        return $this->path;
    }

    public function getQuery()
    {
        // TODO: Implement getQuery() method.
        return $this->query;
    }

    public function getFragment()
    {
        // TODO: Implement getFragment() method.
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
        $this->scheme = $scheme;
        return $this;
    }

    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
        $info = $user;
        if ($password != '') {
            $info .= ':' . $password;
        }

        $this->userInfo = $info;
        return $this;
    }

    public function withHost($host)
    {
        // TODO: Implement withHost() method.
//        $host = strtolower($host);

        $this->host = $host;
        return $this;
    }

    public function withPort($port)
    {
        // TODO: Implement withPort() method.

        $this->port = $port;
        return $this;
    }

    public function withPath($path)
    {
        // TODO: Implement withPath() method.
        $this->path = $path;
        return $this;
    }

    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
        $this->query = $query;
        return $this;
    }

    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
        $this->fragment = $fragment;
        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        $uri = '';
        // weak type checks to also accept null until we can add scalar type hints
        if ($this->scheme != '') {
            $uri .= $this->scheme . ':';
        }
        if ($this->getAuthority() != '' || $this->scheme === 'file') {
            $uri .= '//' . $this->getAuthority();
        }
        $uri .= $this->path;
        if ($this->query != '') {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment != '') {
            $uri .= '#' . $this->fragment;
        }
        return $uri;
    }
}
