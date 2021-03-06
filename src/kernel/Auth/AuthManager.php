<?php

namespace Lxh\Auth;

use Lxh\Auth\Conductors\ChecksRoles;
use Lxh\Auth\Conductors\FindRoles;
use Lxh\Auth\Database\Role;
use Lxh\Cache\CacheInterface;
use Lxh\Cache\File;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Mvc\Model;
use Lxh\Auth\Cache\Storage;
use Lxh\Auth\Database\Models;
use Lxh\Support\Collection;
use Lxh\Auth\Database\Ability as AbilityModel;

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
     * @var CacheInterface
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

    /**
     * @var ChecksRoles
     */
    protected $checksRoles;

    /**
     * @var bool
     */
    protected $enable = true;

    /**
     * @var string
     */
    protected $menuModelClass;

    /**
     * 缓存对象
     *
     * @var mixed
     */
    protected static $cacheStorage;

    public function __construct(Model $user = null)
    {
        $this->user = $user ?: __admin__();

        if (! $this->user->getId()) {
            throw new InvalidArgumentException('Invalid user model.');
        }

        $this->usesCached = config('admin.auth.use-cache', true);

        if ($this->usesCached) {
            $this->cache = cache_factory()->get(config('admin.auth.cache-channel', 'admin-auth'));
        }

        $this->enable = config('admin.auth.enable', true);
    }

    /**
     * 手动注入权限
     *
     * @param array $abilities
     * @return $this
     */
    public function attach(array $abilities)
    {
        foreach ($abilities as &$ability) {
            $this->abilities[$ability] = [
                'id'        => '',
                'slug'      => $ability,
                'forbidden' => 0,
            ];
        }

        $this->abilities = new Collection($this->abilities);
        $this->enable    = true;

        return $this;
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
        if (method_exists($this->user, 'isAdmin')) {
            return $this->user->isAdmin();
        }
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
            $user = __admin__();
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
     * Start a chain, to check if the given authority has a certain role.
     *
     * @return \Lxh\Auth\Conductors\ChecksRoles
     */
    public function is()
    {
        return $this->checksRoles ?:
            ($this->checksRoles = new Conductors\ChecksRoles($this->user, $this->clipboard()));
    }

    /**
     * @return Model|\Lxh\Mvc\Model
     */
    public function user()
    {
        return $this->user;
    }

    /**
     *
     * @return CacheInterface
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
     * 删除有关权限的缓存
     *
     * @return $this
     */
    public function refreshForAbility(AbilityModel $ability)
    {
        if (! $this->usesCached) {
            return $this;
        }

        foreach ($ability->findRolesIds()->all() as &$id) {
            $role = Models::role();
            $this->refreshForRole($role->setId($id));
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
     * 由于数据库不区分大小写
     * 所以建议所有权限唯一标识均采用小写中划线模式命名
     *
     * @param $name
     * @return string
     */
    public static function normalizName($name)
    {
        return slug($name);
    }

    /**
     * Determine if the given ability is allowed.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability)
    {
        if (! $this->enable) {
            return true;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        $ability = static::normalizName($ability);

        $abilities = $this->abilities()->all();

        if (! isset($abilities[$ability])) return false;

        return $abilities[$ability]['forbidden'] == 0 ? true : false;
    }

    /**
     * 菜单管理对象
     *
     * @return Menu
     */
    public function menu()
    {
        return $this->menu ?: ($this->menu = new Menu($this, $this->menuModelClass));
    }

    /**
     * @param $class
     * @return $this
     */
    public function setMenuModel($class)
    {
        $this->menuModelClass = $class;
        return $this;
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
     * 检查详情页查看权限
     *
     * @return bool
     */
    public function detailable($controller = __CONTROLLER__)
    {
        return $this->can($controller . '.' . Ability::DETAIL);
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
        return $this->can($controller . '.' . Ability::BATCH_DELETE);
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
        if (! $this->enable) {
            return false;
        }

        if ($this->isAdministrator()) {
            return false;
        }

        $abilities = $this->abilities()->all();

        if (! isset($abilities[$ability])) return false;

        return $abilities[$ability] == 1 ? true : false;
    }

    /**
     * 获取当前用户所有权限
     *
     * @return Collection
     */
    public function abilities()
    {
        if ($this->abilities === null) {
            $this->abilities = $this->formatAbilitiesArray($this->clipboard()->getAbilities());
        }

        return $this->abilities;
    }

    protected function formatAbilitiesArray(Collection $abilities)
    {
        $content = [];
        foreach ($abilities->all() as &$row) {
            $content[$row['slug']] = $row;
        }
        return new Collection($content);
    }

    /**
     * 获取根据角色分组的权限列表
     *
     * @return Collection
     */
    public function getAbilitiesGroupByRoles()
    {
        $abilities   = $this->clipboard()->getAbilities();
        $roles       = $this->roles();
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
     * @return $this
     */
    public function refreshForRole(Role $role)
    {
        if (! $this->usesCached) return $this;

        foreach ($role->findUsersIds()->all() as &$id) {
            $user = Models::user();

            $this->refreshFor($user->setId($id));
        }

        return $this;
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
