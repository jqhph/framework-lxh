<?php
/**
 * 用户模型
 *
 * @author Jqh
 * @date   2017/6/27 16:37
 */

namespace Lxh\Admin\Models;

use Lxh\Contracts\Container\Container;
use Lxh\Helper\Entity;
use Lxh\MVC\Model;
use Lxh\MVC\Session;
use Lxh\Support\Password;

class Admin extends Session
{
    /**
     * 默认查询的字段
     *
     * @var string|array
     */
    protected $defaultSelectFields = ['id', 'is_admin', 'username', 'first_name', 'last_name', 'email', 'mobile', 'sex', 'avatar', 'created_at'];

    /**
     * 权限实体类型
     *
     * @var int
     */
    protected $morphType = 1;

    /**
     * 注册
     *
     * @param  array $options 注册参数
     * @return bool
     */
    public function register(array & $options, $ip)
    {
        $this->username      = $options['username'];
        $this->password      = Password::encrypt($options['password']);
        $this->reg_ip        = $ip;
        $this->last_login_ip = $ip;
        $this->created_at    = time();

        return $this->add();
    }

    public function beforeAdd(array &$input)
    {
        $input['created_at'] = time();
        $input['created_by_id'] = admin()->getId() ?: 0;

    }

    /**
     * 检测用户名是否存在
     *
     * @param $username
     * @return bool
     */
    public function userExists($username)
    {
        if ($this->query()->select('id')->where('username', $username)->findOne()) {
            return true;
        }
        return false;
    }

    protected function beforeSave($id, array & $data)
    {
        unset($data['cookie']);
        unset($data['session']);
    }

    /**
     * 登录
     *
     * @param  string $username
     * @param  string $password
     * @param  string $remember 是否记住登陆
     * @param  bool   $skipVertify 是否跳过用户信息验证
     * @return bool
     */
    public function login($account, $password, $remember = false, $skipVertify = false)
    {
        $query = $this->query();

        array_push($this->defaultSelectFields, 'password');

        $userData = $query
            ->select($this->defaultSelectFields)
            ->where(
                [
                    'deleted' => 0, 'OR' => ['username' => & $account, 'email' => & $account, 'mobile' => & $account]
                ]
            )
            ->findOne();

        if (! $userData) {
            return false;
        }

        // 注入用户信息
        $this->attach($userData);

        // 验证密码是否正确
        if (! $skipVertify && ! Password::verify($password, $userData['password'])) {
            return false;
        }

        // 保存用户信息到session
        $this->saveSession();

        if ($remember) {
            $this->saveCookie();
        }

        return true;
    }


    /**
     * @return int
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * 判断是否已经登录
     *
     * @return int
     */
    public function auth()
    {
        return $this->id;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

}
