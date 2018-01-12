<?php

namespace Lxh\Auth;

use Lxh\Auth\Conductors\FindRoles;
use Lxh\Auth\Database\Role;
use Lxh\Cache\File;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\MVC\Model;
use Lxh\Auth\Cache\Store;
use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Models;
use Lxh\Support\Collection;
use Lxh\Auth\Ability;

class AuthManager
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @var bool
     */
    protected $usesCached = true;

    /**
     * @var Model
     */
    protected $user;

    /**
     * @var Store
     */
    protected $cache;

    /**
     * @var Collection
     */
    protected $roles = null;

    /**
     * @var Collection
     */
    protected $abilities = null;

    /**
     * @var CachedClipboard|\Lxh\Auth\Clipboard
     */
    protected $clipboard;

    /**
     * @var Menu
     */
    protected $menu;

    public function __construct(Model $user = null)
    {
        $this->user = $user ?: admin();

        if (! $this->user->getId()) {
            throw new InvalidArgumentException('Invalid user model.');
        }

        $this->usesCached = config('admin.auth.cache', true);

        if ($this->usesCached) {
            $this->cache = new Store($this->createCacheStore());
        }
    }

    protected function createCacheStore()
    {
        return new File();
    }

    /**
     * @return CachedClipboard|\Lxh\Auth\Clipboard
     */
    protected function clipboard()
    {
        if ($this->clipboard) return $this->clipboard;

        if ($this->usesCached) {
            $this->clipboard = new CachedClipboard($this->user, $this->cache);
        } else {
            $this->clipboard = new Clipboard($this->user);
        }

        return $this->clipboard;
    }

    /**
     * 判断是否是超级管理员
     *
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->user->isAdmin();
    }

    /**
     * Resolve a Auth instance.
     *
     * @param  mixed  $user
     * @return static
     */
    public static function resolve(Model $user = null)
    {
        if (! $user) {
            $user = admin();
        }
        if (! $id = $user->getId()) {
            throw new InvalidArgumentException('Invalid user model.');
        }

        if (isset(static::$instances[$id])) {
            return static::$instances[$id];
        }

        return static::$instances[$id] = new static($user);
    }

    /**
     * Create a new Auth instance.
     *
     * @param  Model  $user
     * @return static
     */
    public static function create(Model $user = null)
    {
        return new static($user);
    }

    /**
     * 给用户分配能力
     *
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\GivesAbilities
     */
    public function allow()
    {
        return new Conductors\GivesAbilities($this->user);
    }

    /**
     * Start a chain, to disallow the given authority an ability.
     *
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\RemovesAbilities
     */
    public function disallow($authority)
    {
        return new Conductors\RemovesAbilities($authority);
    }

    /**
     * Start a chain, to forbid the given authority an ability.
     *
     * @return \Lxh\Auth\Conductors\GivesAbilities
     */
    public function forbid()
    {
        return new Conductors\ForbidsAbilities($this->user);
    }

    /**
     * Start a chain, to unforbid the given authority an ability.
     *
     * @return \Lxh\Auth\Conductors\RemovesAbilities
     */
    public function unforbid()
    {
        return new Conductors\UnforbidsAbilities($this->user);
    }

    /**
     * 给用户分配角色
     *
     * @param  \Lxh\Auth\Database\Role|\Lxh\Support\Collection|string  $roles
     * @return \Lxh\Auth\Conductors\AssignsRoles
     */
    public function assign($roles)
    {
        return new Conductors\AssignsRoles($this, $this->user, $roles);
    }

    /**
     * Start a chain, to retract the given role from a model.
     *
     * @param  \Lxh\Support\Collection|\Lxh\Auth\Database\Role|string  $roles
     * @return \Lxh\Auth\Conductors\RemovesRoles
     */
    public function retract($roles = [])
    {
        return new Conductors\RemovesRoles($this, $this->user, $roles);
    }

    /**
     * Start a chain, to sync roles/abilities for the given authority.
     *
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\SyncsRolesAndAbilities
     */
    public function sync($authority)
    {
        return new Conductors\SyncsRolesAndAbilities($authority);
    }

    /**
     * Start a chain, to check if the given authority has a certain role.
     *
     * @return \Lxh\Auth\Conductors\ChecksRoles
     */
    public function is()
    {
        return new Conductors\ChecksRoles($this->user, $this->roles(), $this->clipboard());
    }

    /**
     * @return Model|\Lxh\MVC\Model
     */
    public function user()
    {
        return $this->user;
    }

    /**
     *
     * @return Store
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Clear the cache.
     *
     * @return $this
     */
    public function refresh()
    {
        if ($this->usesCached) {
            $this->clipboard()->refresh();
        }
        return $this;
    }

    /**
     * Clear the cache for the given authority.
     *
     * @param  mixed  $authority
     * @return $this
     */
    public function refreshFor($authority = null)
    {
        if ($this->usesCached) {
            $this->clipboard()->refreshFor($authority);
        }

        return $this;
    }

    /**
     * Clear the cache for all authorities.
     *
     * @return $this
     */
    public function refreshAll()
    {
        if ($this->usesCached) {
            $this->clipboard()->refreshAll();
        }

        return $this;
    }

    /**
     * @return bool|mixed
     */
    public function usesCached()
    {
        return $this->usesCached;
    }

    /**
     * Determine if the given ability is allowed.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability)
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $keyName = Models::getAbilityKeyName();

        $exists = false;
        foreach ($this->abilities()->all() as $name => &$row) {
            if ($name == $ability || get_value($row, $keyName) == $ability) {
                $exists = $row;
                break;
            }
        }
        if (! $exists) return false;

        return $exists['forbidden'] == 0 ? false : true;
    }

    /**
     * @return Menu
     */
    public function menu()
    {
        return $this->menu ?: ($this->menu = new Menu($this));
    }

    /**
     * 检查读权限
     *
     * @return bool
     */
    public function readable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::READ);
    }

    /**
     * 检查创建权限
     *
     * @return bool
     */
    public function createable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::CREATE);
    }

    /**
     * 检查编辑权限
     *
     * @return bool
     */
    public function updateable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::UPDATE);
    }

    /**
     * 检查删除权限
     *
     * @return bool
     */
    public function deleteable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::DELETE);
    }

    /**
     * 检查批量删除权限
     *
     * @return bool
     */
    public function batchDeleteable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::BATCHDELETE);
    }

    /**
     * 检查导出权限
     *
     * @return bool
     */
    public function exportable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::EXPORT);
    }

    /**
     * 检查导入权限
     *
     * @return bool
     */
    public function importable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::IMPORT);
    }

    /**
     * 检查上传权限
     *
     * @return bool
     */
    public function uploadable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::UPLOAD);
    }

    /**
     * Determine if the given ability is denied.
     *
     * @param  string  $ability
     * @return bool
     */
    public function forbidden($ability)
    {
        if ($this->isAdministrator()) {
            return false;
        }

        $keyName = Models::getAbilityKeyName();

        $exists = false;
        foreach ($this->abilities()->all() as $name => &$row) {
            if ($name == $ability || get_value($row, $keyName) == $ability) {
                $exists = $row;
                break;
            }
        }
        if (! $exists) return false;

        return $exists['forbidden'] == 0 ? true : false;
    }

    /**
     * 获取当前用户所有权限
     *
     * @return Collection
     */
    public function abilities()
    {
        if ($this->abilities === null) {
            $this->abilities = $this->clipboard()->getAbilities();
        }

        return $this->abilities;
    }

    /**
     * 获取根据角色分组的权限列表
     *
     * @return Collection
     */
    public function getAbilitiesGroupByRoles()
    {
        $abilities = $this->abilities();
        $roles = $this->roles();
        $roleKeyName = Models::getRoleKeyName();

        return $roles->keyBy(function (&$row) use ($abilities, $roleKeyName) {
            $row['abilities'] = $abilities->filter(function ($ability) use ($row, $roleKeyName) {
                if ($ability['role_id'] == $row[$roleKeyName]) {
                    return $ability;
                }
            });

            return $row['title'];
        });
    }

    /**
     * Determine if the given ability is allowed.
     *
     * Alias for the "can" method.
     *
     * @deprecated
     * @param  string  $ability
     * @return bool
     */
    public function allows($ability)
    {
        return $this->can($ability);
    }

    /**
     * Determine if the given ability is denied.
     *
     * Alias for the "forbidden" method.
     *
     * @deprecated
     * @param  string  $ability
     * @return bool
     */
    public function denies($ability)
    {
        return $this->forbidden($ability);
    }

    /**
     * 获取当前用户所有的角色.
     *
     * @return Collection
     */
    public function roles()
    {
        if ($this->roles !== null) {
            return $this->roles;
        }

        return $this->roles = (new FindRoles($this->user, $this->abilities))->find();
    }

    /**
     * 根据角色清除用户权限缓存
     *
     * @param Role $roles
     * @return void
     */
    public function refreshForRole(Role $role)
    {
        foreach ($role->findUsersIds()->all() as &$id) {
            $user = Models::user();

            $this->refreshFor($user->attach([
                $user->getKeyName() => $id,
            ]));
        }
    }


    /**
     * Set the model to be used for abilities.
     *
     * @param  string  $model
     * @return $this
     */
    public function useAbilityModel($model)
    {
        Models::setAbilitiesModel($model);

        return $this;
    }

    /**
     * Set the model to be used for roles.
     *
     * @param  string  $model
     * @return $this
     */
    public function useRoleModel($model)
    {
        Models::setRolesModel($model);

        return $this;
    }

    /**
     * Set the model to be used for users.
     *
     * @param  string  $model
     * @return $this
     */
    public function useUserModel($model)
    {
        Models::setUsersModel($model);

        return $this;
    }

    /**
     * Set custom table names.
     *
     * @param  array  $map
     * @return $this
     */
    public function tables(array $map)
    {
        Models::setTables($map);

        return $this;
    }

}
