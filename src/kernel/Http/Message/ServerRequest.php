<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ServerRequest extends Request implements ServerRequestInterface
{
    private $attributes = [];

    private $cookieParams = [];

    private $parsedBody;

    private $queryParams = [];

    protected $serverParams;

    protected $uploadedFiles = [];

    public function __construct(StreamInterface $body = null)
    {
        $this->serverParams = & $_SERVER;

        $this->queryParams = & $_GET;

        $this->parsedBody = & $_POST;

        $this->cookieParams = & $_COOKIE;

        $this->initServerHeader();

        parent::__construct([], $body);
    }

    /**
     * 初始化header头信息
     *
     * @return void
     */
    protected function initServerHeader()
    {
        foreach ($this->serverParams as $key => & $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $this->headers[substr($key, 5)] = explode(',', $value);
            }
            if (strpos($key, 'X_') === 0) {
                $this->headers[$key] = explode(',', $value);
            }
        }
    }


    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookieParams;

    }

    public function withCookieParams(array $cookies)
    {
        $this->backupsOriginServer();

        $this->cookieParams = $cookies;
        return $this;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query)
    {
        $this->backupsOriginServer();

        $this->queryParams = $query;
        return $this;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile($name)
    {
        return isset($this->uploadedFiles[$name]) ? $this->uploadedFiles[$name] : null;
    }

    /**
     * @param array $uploadedFiles must be array of UploadFile Instance
     * @return ServerRequest
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->backupsOriginServer();

        $this->uploadedFiles = $uploadedFiles;
        return $this;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        $this->backupsOriginServer();

        $this->parsedBody = &$data;
        return $this;
    }

    public function getAttributes()
    {
        // TODO: Implement getAttributes() method.
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        // TODO: Implement getAttribute() method.
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
        $this->attributes[$name] = $value;
        return $this;
    }

    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
        unset($this->attributes[$name]);
        return $this;
    }
}
