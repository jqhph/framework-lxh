<?php
/**
 * 用户认证类
 *
 * @author Jqh
 * @date   2017/6/27 15:54
 */

namespace Lxh\Admin\Auth;

use Closure;
use Lxh\Admin\Admin;
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

    /**
     * @param array $options
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $options, Closure $next)
    {
        $admin = admin();
        if (! $admin->getId()) {
            $this->request->url()->save();

            return $this->response->redirect(Admin::url()->login());
        }

        return $next($options);
    }
}
