<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\AuthManager;
use Lxh\MVC\Session;
use Lxh\OAuth\Database\User;
use Lxh\Support\Password;

class Admin extends User
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
    public function register(array &$options, $ip)
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
        parent::beforeAdd($input);

        $input['created_at'] = time();
        $input['created_by_id'] = __admin__()->getId() ?: 0;
        $input['password'] = Password::encrypt($input['password']);

        if (isset($input['roles'])) {
            $this->roles = $input['roles'];
            unset($input['roles']);
        }
    }

    protected function beforeUpdate($id, array &$input)
    {
        parent::beforeUpdate($id, $input);

        if (! empty($input['password'])) {
            $input['password'] = Password::encrypt($input['password']);
        } else {
            unset($input['password']);
        }
        $input['modified_at'] = time();

        if (isset($input['roles'])) {
            $this->roles = $input['roles'];
            unset($input['roles']);
        }
    }

    protected function afterAdd($insertId, array &$input)
    {
        parent::afterAdd($insertId, $input);

        if (! $insertId) return;

        if ($this->roles) {
            AuthManager::resolve($this)->assign($this->roles)->then();
        }
    }

    protected function afterUpdate($id, array &$input, $result)
    {
        parent::afterUpdate($id, $input, $result);

        AuthManager::resolve($this)
            ->assign($this->roles)
            ->retract() // 先重置所有已关联角色
            ->refresh() // 清除缓存
            ->then(); // 执行
    }

    protected function afterDelete($id, $result, $trash)
    {
        parent::afterDelete($id, $result, $trash);

        if (! $id) return;

        AuthManager::resolve($this)
            ->retract() // 清除用户所有已关联角色
            ->refresh() // 刷新缓存
            ->then(); // 执行
    }

    /**
     * 获取用户头像
     *
     * @return mixed
     */
    public function avatar()
    {
        if ($avatar = $this->get('avatar')) {
            return \Lxh\Admin\Admin::url()->image($avatar);
        }
    }

    /**
     * 查找数据方法
     *
     * @return array
     */
    public function find()
    {
        $data = parent::find(); // TODO: Change the autogenerated stub

        if (! $data || ! $this->getId()) return $data;

        $data['roles'] = AuthManager::resolve($this)->roles()->pluck(Models::getRoleKeyName())->all();

        return $data;
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

    public function findForLogined()
    {
        if (! $id = $this->getId()) {
            return false;
        }

        $data = $this->query()
            ->select($this->selectFields)
            ->where([$this->primaryKeyName => $id, 'status' => 1])
            ->findOne();

        $this->fill($data);

        return $data;
    }

    /**
     * 登录
     *
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     * @return array
     */
    public function login($account, $password, array $options = [])
    {
        $this->defaultSelectFields[] = 'password';

        $userData = $this->query()
            ->select($this->defaultSelectFields)
            ->where('status', 1)
            ->whereOr(['username' => &$account])
            ->findOne();

        if (! $userData) {
            return false;
        }

        // 验证密码是否正确
        if (! Password::verify($password, $userData['password'])) {
            return false;
        }

        return $userData;
    }

    /**
     *
     * @return int
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
}
