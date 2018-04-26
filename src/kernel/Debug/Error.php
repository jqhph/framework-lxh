<?php

namespace Lxh\Debug;

class Error
{
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


    public function handle($error)
    {
        $method = getvalue($levelToLogMethods, $error['type'], 'error');

        // 记录错误日志
        logger('exception')->$method('', $error);
    }
}
