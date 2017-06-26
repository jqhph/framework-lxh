<?php
/**
 * 全局初始化操作
 *
 * 触发事件：route.dispatch.after
 *
 * @author Jqh
 * @date   2017/6/15 15:58
 */

namespace Lxh\Home\Events;

use Lxh\Http\Request;
use Lxh\Http\Response;

class GlobalInit
{
    public function __construct()
    {
        // 设置为美国纽约时区
        date_default_timezone_set('America/New_York');
    }

    public function handle(Request $req, Response $resp)
    {

    }
}
