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
use Lxh\OAuth\Exceptions\AuthTokenException;
use Lxh\OAuth\Exceptions\EncryptCodeException;
use Lxh\OAuth\Exceptions\UserNotExistEception;

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
     * @param mixed $options
     * @param Closure $next
     * @return mixed
     */
    public function handle($options, Closure $next)
    {
        $oauth = admin()->oauth();
        
        try {
            if (! $oauth->check()) {
                $this->notlogin();
            }
        } catch (AuthTokenException $e) {
            // 用户可能在其他客户端重复登录
            if ($log = $oauth->logs()->findActiveLatestLoginedLog()) {

            } else {
                $this->notlogin();
            }

        } catch (EncryptCodeException $e) {

        } catch (UserNotExistEception $e) {

        }

        return $next($options);
    }

    protected function notlogin()
    {
        $this->request->url()->save();

        return $this->response->redirect(Admin::url()->login());
    }
}
