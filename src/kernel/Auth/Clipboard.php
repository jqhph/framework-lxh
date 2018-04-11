<?php

namespace Lxh\Auth;

use Lxh\Auth\Access\HandlesAuthorization;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Role;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

class Clipboard
{
    use HandlesAuthorization;

    /**
     * @var Model
     */
    protected $user;

    /**
     * @var Collection
     */
    protected $abilities;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * @var Collection
     */
    protected $rolesName;

    public function __construct(Model $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the given authority has the given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function check($ability)
    {
        return (bool) $this->checkGetId($ability);
    }

    /**
     * Determine if the given authority has the given ability, and return the ability ID.
     *
     * @param  string  $ability
     * @param  Model|string|null  $model
     * @return int|bool|null
     */
    public function checkGetId($ability)
    {
        return $this->getAbilities()->filter(function (&$row) use ($ability) {
            return get_value($row, $ability);
        })->get(Models::getAbilityKeyName());
    }


    /**
     * Check if an authority has the given roles.
     *
     * @param  array|string  $roles
     * @param  string  $boolean
     * @return bool
     */
    public function checkRole($roles, $boolean = 'or')
    {
        $available = $this->getRolesNames()
                        ->intersect(
                            $this->role()->getRoleNames($roles)
                        );

        if ($boolean == 'or') {
            return $available->count() > 0;
        } elseif ($boolean === 'not') {
            return $available->count() === 0;
        }

        return $available->count() == count((array) $roles);
    }

    /**
     * 获取角色标识代码集合
     *
     * @return Collection
     */
    public function getRolesNames()
    {
        return $this->rolesName ?: ($this->rolesName = $this->getRoles()->pluck('slug'));
    }

    /**
     * @return Role
     */
    protected function role()
    {
        return $this->role ?: ($this->role = Models::role());
    }

    /**
     * Get the authority's roles.
     *
     * @return \Lxh\Support\Collection
     */
    public function getRoles()
    {
        if ($this->roles) {
            return $this->roles;
        }

        $ids = $this->getRoleIds()->all();
        if (!$ids) {
            return $this->roles = new Collection();
        }

        return $this->roles = new Collection(
            $this->role()->whereInIds(array_unique($ids))->find()
        );
    }

    /**
     * 获取用户所有角色id
     *
     * @return Collection
     */
    public function getRoleIds()
    {
        return $this->getAbilities()->pluck('role_id');
    }

    /**
     * Get a list of the authority's abilities.
     *
     * @param  Model  $authority
     * @return Collection
     */
    public function getAbilities()
    {
        if ($this->abilities) {
            return $this->abilities;
        }

        return $this->abilities = new Collection(
            Models::ability()->getForAuthority($this->user)
        );
    }

    /**
     * Get a list of the authority's forbidden abilities.
     *
     * @return Collection
     */
    public function getForbiddenAbilities()
    {
        return $this->getAbilities()->filter(function (&$row) {
            return get_value($row, 'forbidden');
        });
    }
}
