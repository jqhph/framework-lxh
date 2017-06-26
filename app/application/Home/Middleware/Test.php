<?php

namespace Lxh\Home\Middleware;

use Closure;

class Test
{
    /**
     * 中间件执行方法
     *
     * @param  mixed   $params 上游传递的参数
     * @param  Closure $next   下级中间件
     * @return void
     */
    public function handle($params, Closure $next)
    {
        // TODO

//        debug($params);

        $next($params);

    }
}
