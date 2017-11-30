<?php

namespace Lxh\Debug;

use Lxh\Contracts\Container\Container;
use Lxh\Contracts\Events\Dispatcher;
use Lxh\Http\Response;

class Shutdown
{
    protected $container;

    protected $events;

    protected $response;

    public function __construct(Container $container, Dispatcher $events, Response $resp)
    {
        $this->container = $container;
        $this->events = $events;
        $this->response = $resp;
    }

    public function handle()
    {
        // 触发程序终结时间
        $this->events->fire('app.shutdown', [$this->response]);

        $this->response->send();
    }

}
