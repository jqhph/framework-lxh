<?php
/**
 * 用户认证类
 *
 * @author Jqh
 * @date   2017/6/27 15:54
 */

namespace Lxh\Admin\Auth;

use Closure;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Contracts\Container\Container;

class User
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container, Request $req, Response $resp)
    {
        $this->container = $container;
        $this->request = $req;
        $this->response = $resp;
    }

    public function handle(array $options, Closure $next)
    {
        if (! admin()->id) {
            return $this->response->redirect('/lxh/login');
        }

        return $next($options);
    }
}
