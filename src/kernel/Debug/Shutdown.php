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

    protected $levelToLogMethods = [
        E_ERROR => 'error',
        E_PARSE => 'addEmergency',
        E_STRICT => 'error',
        E_RECOVERABLE_ERROR => 'addEmergency',
        E_WARNING => 'warning',
        E_NOTICE => 'notice',
        E_CORE_ERROR => 'error',
        E_COMPILE_WARNING => 'warning',
        E_USER_ERROR => 'ERROR',
        E_USER_NOTICE => 'notice',
    ];

    public function __construct(Container $container, Dispatcher $events, Response $resp)
    {
        $this->container = $container;
        $this->events = $events;
        $this->response = $resp;
    }

    public function handle()
    {
        $this->report();

        $this->response();
    }

    protected function report()
    {
        if ($err = error_get_last()) {
            if (in_array($err['type'], (array) config('record-error-info-level'))) {
                $method = get_value($this->levelToLogMethods, $err['type'], 'error');

                // 记录错误日志
                logger('exception')->$method('', $err);
            }
        }

        // 触发程序终结时间
        $this->events->fire('app.shutdown', [$this->response, & $err]);
    }

    protected function response()
    {
        if (! $this->response->sent()) {
            $this->response->send();
        }
    }
}
