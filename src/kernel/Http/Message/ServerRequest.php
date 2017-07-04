<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    private $attributes = [];

    private $cookieParams = [];

    private $parsedBody;

    private $queryParams = [];

    private $serverParams;

    protected $uploadedFiles = [];

    public function __construct(StreamInterface $body = null)
    {
        $this->serverParams = & $_SERVER;

        $this->queryParams = & $_GET;

        $this->parsedBody = & $_POST;

        $this->cookieParams = & $_COOKIE;

        parent::__construct([], $body);
    }

    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        // TODO: Implement getCookieParams() method.
        return $this->cookieParams;

    }

    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
        $this->backupsOriginServer();

        $this->cookieParams = $cookies;
        return $this;
    }

    public function getQueryParams()
    {
        // TODO: Implement getQueryParams() method.
        return $this->queryParams;
    }

    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
        $this->backupsOriginServer();

        $this->queryParams = $query;
        return $this;
    }

    public function getUploadedFiles()
    {
        // TODO: Implement getUploadedFiles() method.
        return $this->uploadedFiles;
    }

    /**
     * @return UploadedFileInterface
     */
    public function getUploadedFile($name)
    {
        // TODO: Implement getUploadedFiles() method.
        return isset($this->uploadedFiles[$name]) ? $this->uploadedFiles[$name] : null;
    }

    /**
     * @param array $uploadedFiles must be array of UploadFile Instance
     * @return ServerRequest
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
        $this->backupsOriginServer();

        $this->uploadedFiles = $uploadedFiles;
        return $this;
    }

    public function getParsedBody()
    {
        // TODO: Implement getParsedBody() method.
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
        $this->backupsOriginServer();

        $this->parsedBody = $data;
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
