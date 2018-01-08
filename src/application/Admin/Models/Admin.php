<?php
/**
 * 用户模型
 *
 * @author Jqh
 * @date   2017/6/27 16:37
 */

namespace Lxh\Admin\Models;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
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
     * @var array
     */
    protected $roles = [];

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
        $input['password'] = Password::encrypt($input['password']);

        $this->roles = $input['roles'];
        unset($input['roles']);
    }

    protected function beforeSave($id, array &$input)
    {
        if (! empty($input['password'])) {
            $input['password'] = Password::encrypt($input['password']);
        } else {
            unset($input['password']);
        }
        $input['modified_at'] = time();

        $this->roles = $input['roles'];
        unset($input['roles']);
    }

    protected function afterAdd($insertId, array &$input)
    {
        if (! $insertId) return;

        $this->assignRoles();
    }

    protected function afterSave($id, array &$input, $result)
    {
        $this->assignRoles();
    }

    protected function afterDelete($id, $result)
    {
        if (! $id) return;

        // 清除用户所有角色
        AuthManager::create($this)->retract()->then();
    }

    /**
     * 给用户关联角色
     * 先重置再关联
     */
    protected function assignRoles()
    {
        if (! $this->roles) return;

        AuthManager::create($this)
            ->assign($this->roles)
            ->retract()
            ->then();
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
            ->where('deleted', 0)
            ->whereOr(['username' => & $account, 'email' => & $account, 'mobile' => & $account])
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
        return $this->getId();
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

}
