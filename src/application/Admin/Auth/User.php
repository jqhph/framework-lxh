<?php
/**
 * 用户认证类
 *
 * @author Jqh
 * @date   2017/6/27 15:54
 */

namespace Lxh\Admin\Auth;

use Closure;
use Lxh\Admin\Admin as AdminCreate;
use Lxh\Http\Response;
use Lxh\Http\Request;
use Lxh\Contracts\Container\Container;
use Lxh\OAuth\Exceptions\AuthTokenException;
use Lxh\OAuth\Exceptions\EncryptCodeException;
use Lxh\OAuth\Exceptions\UserNotExistException;

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
        $oauth = __admin__()->oauth();

        try {
            if (! $oauth->check()) {
                return $this->notlogin();
            }

        } catch (AuthTokenException $e) {
            // 用户可能在其他客户端重复登录
            if ($log = $oauth->logs()->findActiveLatestLoginedLog()) {
                $msg = sprintf(
                    "检测到[%s]您在另一台电脑[%s]登录此账号，如非本人操作，请及时修改密码！",
                    date('Y-m-d H:i:s', $log['created_at']),
                    $log['ip']
                );

                if ($this->request->isIframe()) {
                    return $this->showMessage($msg);

                }
                if ($this->request->isAjax()) {
                    return $this->responseForAjax($msg);

                }

            }
            return $this->notlogin();

        } catch (EncryptCodeException $e) {
            if ($this->request->isIframe()) {
                return $this->showMessage('无效token！');

            }
            if ($this->request->isAjax()) {
                return $this->responseForAjax('无效token！');

            }

        } catch (UserNotExistException $e) {
            if ($this->request->isIframe()) {
                return $this->showMessage('用户不存在！');

            }
            if ($this->request->isAjax()) {
                return $this->responseForAjax('用户不存在！');

            }

        }

        // 用户已登录
        return $next($options);
    }

    protected function responseForAjax($msg = '')
    {
        $this->response->withStatus(401);
        
        return $msg;
    }

    protected function showMessage($msg)
    {
        $url = AdminCreate::url()->login();

        return "<script>parent.layer.alert('$msg', {
                icon: 7,
                title: '提示',
                yes: function (e) {
                    parent.window.location.href = '{$url}';
                }
            });</script>";
    }

    protected function notlogin()
    {
        $url = AdminCreate::url()->login();
        if ($this->request->isIframe()) {
            return "<script>parent.window.location.href = '{$url}';</script>";
        } 
        if ($this->request->isAjax()) {
            return $this->responseForAjax();
        }
        
        $this->request->url()->save();

        return $this->response->redirect($url);
    }
}
