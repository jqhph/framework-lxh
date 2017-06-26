<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 *
 * HTTP 消息值得是客户端发起的「请求」和服务器端返回的「响应」，此接口
 * 定义了他们通用的方法。
 *
 * HTTP 消息是被视为无法修改的，所有能修改状态的方法，都 **必须** 有一套
 * 机制，在内部保持好原有的内容，然后把修改状态后的信息返回。
 *
 * @see http://www.ietf.org/rfc/rfc7230.txt
 * @see http://www.ietf.org/rfc/rfc7231.txt
 */
class Message implements MessageInterface
{
    protected $protocolVersion = '1.1';

    protected $headers = [];

    protected $body;

    protected $firstTimeReadHeader = true;

    /**
     * 备份原始server
     * @var static
     */
    protected $originServer;

    public function __construct(array $headers = [], StreamInterface $body = null)
    {
        if ($headers) {
            $this->headers = $headers;
        }
        if ($body) {
            $this->body = $body;
        }
    }

    /**
     * 获取字符串形式的 HTTP 协议版本信息
     *
     * 字符串必须包含 HTTP 版本数字，如："1.1", "1.0"。
     *
     * @return string HTTP 协议版本
     */
    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
        return $this->protocolVersion;
    }


    /**
     * 返回指定 HTTP 版本号的消息实例。
     *
     * 传参的版本号必须 **只** 包含 HTTP 版本数字，如："1.1", "1.0"。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 消息对象，然后返回
     * 一个新的带有传参进去的 HTTP 版本的实例
     *
     * @param string $version HTTP 版本信息
     * @return self
     */
    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
        $this->backupsOriginServer();

        $this->protocolVersion = $version;
        return $this;
    }


    public function getOriginMessageServer()
    {
        return $this->originServer;
    }

    /**
     * 备份原始http server对象
     *
     * @return void
     */
    protected function backupsOriginServer()
    {
        if ($this->originServer) {
            return;
        }
        $this->originServer = clone $this;
    }

    /**
     * 获取所有的头信息
     *
     * 返回的二维数组中，第一维数组的「键」代表单条头信息的名字，「值」是
     * 以数据形式返回的，见以下实例：
     *
     *     // 把「值」的数据当成字串打印出来
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ': ' . implode(', ', $values);
     *     }
     *
     *     // 迭代的循环二维数组
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * 虽然头信息是没有大小写之分，但是使用 `getHeaders()` 会返回保留了原本
     * 大小写形式的内容。
     *
     * @return string[][] 返回一个两维数组，第一维数组的「键」 **必须** 为单条头信息的
     *     名称，对应的是由字串组成的数组，请注意，对应的「值」 **必须** 是数组形式的。
     */
    public function getHeaders()
    {
        // TODO: Implement getHeaders() method.
        $this->initServerHeader();

        return $this->headers;
    }

    /**
     * 初始化header头信息
     *
     * @return void
     */
    protected function initServerHeader()
    {
        if (! $this->firstTimeReadHeader) {
            return;
        }

        foreach ($_SERVER as $key => & $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $this->headers[strtolower(str_replace('_', '-',  substr($key, 5)))] = explode(',', $value);
            }
            if (strpos($key, 'X_') === 0) {
                $this->headers[strtolower($key)] = explode(',', $value);
            }
        }

        $this->firstTimeReadHeader = false;
    }

    /**
     * 检查是否头信息中包含有此名称的值，不区分大小写
     *
     * @param string $name 不区分大小写的头信息名称
     * @return bool 找到返回 true，未找到返回 false
     */
    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
        $this->initServerHeader();

        return isset($this->headers[strtolower($name)]);
    }


    /**
     * 根据给定的名称，获取一条头信息，不区分大小写，以数组形式返回
     *
     * 此方法以数组形式返回对应名称的头信息。
     *
     * 如果没有对应的头信息，**必须** 返回一个空数组。
     *
     * @param string $name 不区分大小写的头部字段名称。
     * @return string[] 返回头信息中，对应名称的，由字符串组成的数组值，如果没有对应
     * 	的内容，**必须** 返回空数组。
     */
    public function getHeader($name)
    {
        // TODO: Implement getHeader() method.
        $this->initServerHeader();

        $name = strtolower($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : array();
    }

    /**
     * 根据给定的名称，获取一条头信息，不区分大小写，以逗号分隔的形式返回
     *
     * 此方法返回所有对应的头信息，并将其使用逗号分隔的方法拼接起来。
     *
     * 注意：不是所有的头信息都可使用逗号分隔的方法来拼接，对于那些头信息，请使用
     * `getHeader()` 方法来获取。
     *
     * 如果没有对应的头信息，此方法 **必须** 返回一个空字符串。
     *
     * @param string $name 不区分大小写的头部字段名称。
     * @return string 返回头信息中，对应名称的，由逗号分隔组成的字串，如果没有对应
     * 	的内容，**必须** 返回空字符串。
     */
    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
        $this->initServerHeader();
        $name = strtolower($name);
        return isset($this->headers[$name]) ? implode("; ", $this->headers[$name]) : '';
    }

    /**
     * 返回指定头信息「键/值」对的消息实例。
     *
     * 虽然头信息是不区分大小写的，但是此方法必须保留其传参时的大小写状态，并能够在
     * 调用 `getHeaders()` 的时候被取出。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 消息对象，然后返回
     * 一个新的带有传参进去头信息的实例
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
        $this->backupsOriginServer();

        $name = strtolower($name);

        $this->headers[$name] = (array) $value;
        return $this;
    }

    /**
     * 返回一个头信息增量的 HTTP 消息实例。
     *
     * 原有的头信息会被保留，新的值会作为增量加上，如果头信息不存在的话，会被加上。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 消息对象，然后返回
     * 一个新的修改过的 HTTP 消息实例。
     *
     * @param string $name 不区分大小写的头部字段名称。
     * @param string|string[] $value 头信息对应的值。
     * @return self
     * @throws \InvalidArgumentException 头信息字段名称非法时会被抛出。
     * @throws \InvalidArgumentException 头信息的值非法的时候，会被抛出。
     */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
        $this->backupsOriginServer();

        $name = strtolower($name);

        if (isset($this->headers[$name])) {
            $this->headers[$name] = array_merge($this->headers[$name], (array) $value);
        } else {
            $this->headers[$name] = (array) $value;
        }
        return $this;
    }

    /**
     * 返回被移除掉指定头信息的 HTTP 消息实例。
     *
     * 头信息字段在解析的时候，**必须** 保证是不区分大小写的。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 消息对象，然后返回
     * 一个新的修改过的 HTTP 消息实例。
     *
     * @param string $name 不区分大小写的头部字段名称。
     * @return self
     */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
        $this->backupsOriginServer();

        $name = strtolower($name);

        unset($this->headers[$name]);

        return $this;
    }

    /**
     * 获取 HTTP 消息的内容。
     *
     * @return StreamInterface 以数据流的形式返回。
     */
    public function getBody()
    {
        // TODO: Implement getBody() method.
        if ($this->body == null) {
            $this->body = new Stream('');
        }
        return $this->body;
    }

    /**
     * 返回拼接了内容的 HTTP 消息实例。
     *
     * 内容 **必须** 是 StreamInterface 接口的实例。
     *
     * 此方法在实现的时候，**必须** 保留原有的不可修改的 HTTP 消息对象，然后返回
     * 一个新的修改过的 HTTP 消息实例。
     *
     * @param StreamInterface $body 数据流形式的内容。
     * @return self
     * @throws \InvalidArgumentException 当消息内容不正确的时候。
     */
    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
        $this->backupsOriginServer();

        $this->body = $body;
        return $this;
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
        $this->initServerHeader();

    }
}
