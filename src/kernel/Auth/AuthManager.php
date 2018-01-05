<?php

namespace Lxh\Auth;

use Lxh\Admin\MVC\Model;
use Lxh\Auth\Cache\Store;
use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Models;
use Lxh\Support\Collection;

class AuthManager
{
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
     * @var array
     */
    protected $roles = [];

    /**
     * @var array
     */
    protected $abilities = null;

    /**
     * @var CachedClipboard|\Lxh\Auth\Clipboard
     */
    protected $clipboard;

    public function __construct(Model $user = null)
    {
        if (! $user) {
            $user = admin();
        }
        $this->user = $user;

        $this->usesCached = config('admin.auth.cache', true);

        if ($this->usesCached) {
            $this->cache = new Store();
        }
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
     * Create a new Auth instance.
     *
     * @param  mixed  $user
     * @return static
     */
    public static function create($user = null)
    {
        return new static($user);
    }

    /**
     * Start a chain, to allow the given authority an ability.
     *
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\GivesAbilities
     */
    public function allow($authority)
    {
        return new Conductors\GivesAbilities($authority);
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
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\GivesAbilities
     */
    public function forbid($authority)
    {
        return new Conductors\ForbidsAbilities($authority);
    }

    /**
     * Start a chain, to unforbid the given authority an ability.
     *
     * @param  Model|string  $authority
     * @return \Lxh\Auth\Conductors\RemovesAbilities
     */
    public function unforbid($authority)
    {
        return new Conductors\UnforbidsAbilities($authority);
    }

    /**
     * Start a chain, to assign the given role to a model.
     *
     * @param  \Lxh\Auth\Database\Role|\Lxh\Support\Collection|string  $roles
     * @return \Lxh\Auth\Conductors\AssignsRoles
     */
    public function assign($roles)
    {
        return new Conductors\AssignsRoles($roles);
    }

    /**
     * Start a chain, to retract the given role from a model.
     *
     * @param  \Lxh\Support\Collection|\Lxh\Auth\Database\Role|string  $roles
     * @return \Lxh\Auth\Conductors\RemovesRoles
     */
    public function retract($roles)
    {
        return new Conductors\RemovesRoles($roles);
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
     * @param  Model  $authority
     * @return \Lxh\Auth\Conductors\ChecksRoles
     */
    public function is(Model $authority)
    {
        return new Conductors\ChecksRoles($authority, $this->clipboard);
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
     * @param  mixed  $authority
     * @return $this
     */
    public function refresh($authority = null)
    {
        if ($this->usesCached) {
            $this->clipboard->refresh($authority);
        }
        return $this;
    }

    /**
     * Clear the cache for the given authority.
     *
     * @param  mixed  $authority
     * @return $this
     */
    public function refreshFor($authority)
    {
        if ($this->usesCached) {
            $this->clipboard->refreshFor($authority);
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

        if (isset($this->abilities[$ability])) {
            return $this->abilities[$ability]['forbidden'] == 0;
        }

        if ($this->abilities !== null) {
            return false;
        }

        $this->abilities = $this->clipboard->getAbilities();

        return (isset($this->abilities[$ability]) && $this->abilities[$ability]['forbidden'] == 0);
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

        if (isset($this->abilities[$ability])) {
            return $this->abilities[$ability]['forbidden'] == 1;
        }

        if ($this->abilities !== null) {
            return false;
        }

        $this->abilities = $this->clipboard->getAbilities();

        return (isset($this->abilities[$ability]) && $this->abilities[$ability]['forbidden'] == 1);
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
     * Get an instance of the role model.
     *
     * @param  array  $attributes
     * @return \Lxh\Auth\Database\Role
     */
    public function role(array $attributes = [])
    {
        return Models::role($attributes);
    }

    /**
     * Get an instance of the ability model.
     *
     * @param  array  $attributes
     * @return \Lxh\Auth\Database\Ability
     */
    public function ability(array $attributes = [])
    {
        return Models::ability($attributes);
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
