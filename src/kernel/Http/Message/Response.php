<?php

namespace Lxh\Http\Message;

use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    private $statusCode = 200;

    private $reasonPhrase = 'OK';

    protected $firstTimeReadHeader = false;

    public function getStatusCode()
    {
        // TODO: Implement getStatusCode() method.
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
        if (! Status::vertify($code)) {
            throw new \InvalidArgumentException('Invalid http status code.');
        }

        $this->statusCode = $code;
        if (empty($reasonPhrase)) {
            $this->reasonPhrase = Status::getReasonPhrase($this->statusCode);
        } else {
            $this->reasonPhrase = $reasonPhrase;
        }
        return $this;

    }

    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
        return $this->reasonPhrase;
    }
}
