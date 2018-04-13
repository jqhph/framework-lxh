<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\Access\AuthorizationException;
use Lxh\Auth\AuthManager;
use Lxh\RequestAuth\Database\User;
use Lxh\RequestAuth\Exceptions\UserNotExistException;
use Lxh\Support\Collection;
use Lxh\Support\Password;

class Admin extends User
{
    const ACTIVE_STATUS = 1;

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
     * 后台用户登录token加密追加值
     *
     * @var int
     */
    protected $encryptType = 1;

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
        $this->reg_ip        = ip2long($ip);
        $this->last_login_ip = ip2long($ip);
        $this->created_at    = time();

        return $this->add();
    }

    public function beforeAdd(array &$input)
    {
        parent::beforeAdd($input);

        $input['created_at']    = time();
        $input['created_by_id'] = __admin__()->getId() ?: 0;
        $input['password']      = Password::encrypt($input['password']);

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

        $this->setId($insertId);

        // 记录操作日志
        $adminAction = operations_logger()->adminAction();

        $adminAction->setInsert();
        $adminAction->input = $this->toJson();
        $adminAction->table = $this->tableName;
        $adminAction->add();
    }

    protected function afterUpdate($id, array &$input, $result)
    {
        parent::afterUpdate($id, $input, $result);

        AuthManager::resolve($this)
            ->assign($this->roles)
            ->retract() // 先重置所有已关联角色
            ->refresh() // 清除缓存
            ->then(); // 执行

        if ($result) {
            // 记录操作日志
            operations_logger()->adminAction($this)->setUpdate()->add();
        }
    }

    protected function afterToTrash($id, $result)
    {
        if ($result) {
            operations_logger()->adminAction($this)->setMoveToTrash()->add();
        }
    }

    protected function afterBatchToTrash(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setBatchMoveToTrash()->add();
        }
    }

    protected function afterRestore(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setRestore()->add();
        }
    }

    public function afterBatchDelete(array &$ids, $effect, $trash)
    {
        parent::afterBatchDelete($ids, $effect, $trash);
        
        if ($effect) {
            $adminAction = operations_logger()->adminAction($this);

            $adminAction->input = implode(',', $ids);
            $adminAction->setBatchDelete()->add();
        }
    }

    protected function afterDelete($id, $result, $trash)
    {
        parent::afterDelete($id, $result, $trash);

        if (! $id) return;

        AuthManager::resolve($this)
            ->retract() // 清除用户所有已关联角色
            ->refresh() // 刷新缓存
            ->then(); // 执行

        if ($result) {
            if ($trash) {
                $table = $this->trashTableName;
            } else {
                $table = $this->tableName;
            }
            $actionAdmin = operations_logger()->adminAction($this);

            $actionAdmin->table = $table;

            $actionAdmin->setDelete()->add();
        }
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
        if ($this->query()->select($this->getKeyName())->where('username', $username)->findOne()) {
            return true;
        }
        return false;
    }

    /**
     * 查找
     *
     * @return Collection
     */
    public function findNameKeyById()
    {
        $keyName = $this->getKeyName();

        return (new Collection(
            $this->query()->select($keyName.',username,first_name,last_name')->find()
        ))->keyBy($keyName)->map(function ($v, $k) {
            return ($v['first_name'] . ' ' . $v['last_name']) ?: $v['username'];
        });
    }

    /**
     * @return array|bool
     */
    public function findForLogged()
    {
        if (! $id = $this->getId()) {
            return false;
        }

        $data = $this->query()
            ->select($this->selectFields)
            ->where([$this->primaryKeyName => $id, 'status' => static::ACTIVE_STATUS])
            ->findOne();

        $this->attach($data);

        return $data;
    }

    /**
     * @return int
     */
    public function getEncryptType()
    {
        return $this->encryptType;
    }

    /**
     * 登录
     *
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     * @return array
     *
     * @throws AuthorizationException
     * @throws UserNotExistException
     */
    public function login($account, $password, array $options = [])
    {
        $this->defaultSelectFields[] = 'password';

        $userData = $this->query()
            ->select($this->defaultSelectFields)
            ->where('status', static::ACTIVE_STATUS)
            ->whereOr(['username' => &$account])
            ->findOne();

        if (! $userData) {
            throw new UserNotExistException(trans('No such username exists!'));
        }

        // 验证密码是否正确
        if (! Password::verify($password, $userData['password'])) {
            throw new AuthorizationException(trans('Password incorrect!'));
        }

        $this->afterLogin($userData);

        return $userData;
    }

    protected function afterLogin(array &$items)
    {
        // 保存用户最后登录ip
        if ($id = $items[$this->getKeyName()]) {
            $this->setId($id);
            $this->last_login_ip = ip2long(request()->ip());
        }
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
