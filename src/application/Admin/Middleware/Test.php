<?php

namespace Lxh\Admin\Middleware;

use Closure;

class Test
{
    /**
     * 中间件执行方法
     *
     * @param  mixed   $params 上游传递的参数
     * @param  Closure $next   下级中间件
     * @return mixed
     */
    public function handle($params, Closure $next)
    {

        return $next($params);

    }
}
