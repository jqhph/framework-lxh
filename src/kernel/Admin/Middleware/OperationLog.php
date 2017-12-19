<?php

namespace Lxh\Admin\Middleware;

use Lxh\Admin\Facades\Admin;
use Lxh\Http\Request;

class OperationLog
{
    /**
     * Handle an incoming request.
     *
     * @param \Lxh\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (config('admin.operation_log') && Admin::user()) {
            $log = [
                'user_id' => Admin::user()->id,
                'path'    => $request->path(),
                'method'  => $request->method(),
                'ip'      => $request->getClientIp(),
                'input'   => json_encode($request->input()),
            ];

            \Lxh\Admin\Auth\Database\OperationLog::create($log);
        }

        return $next($request);
    }
}
