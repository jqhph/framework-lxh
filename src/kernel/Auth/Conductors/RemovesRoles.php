<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Role;
use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;
use Lxh\ORM\Query;
use Lxh\Support\Arr;
use Lxh\Support\Collection;

class RemovesRoles
{
    /**
     * @var Model
     */
    protected $authority;

    /**
     * The roles to be removed.
     *
     * @var array
     */
    protected $roles;

    /**
     * Constructor.
     *
     * @param \Lxh\Support\Collection|\Lxh\Auth\Database\Role|string  $roles
     */
    public function __construct(Model $authority, $roles)
    {
        $this->authority = $authority;

        $this->roles = Helpers::toArray($roles);
    }

    /**
     * Remove the role from the given authority.
     *
     * @param  Model|array|int  $authority
     * @return mixed
     */
    public function from($authority)
    {
        if (! ($roleIds = $this->getRoleIds())) {
            return false;
        }

        $authorities = is_array($authority) ? $authority : [$authority];

        return $this->retractRoles($roleIds, $authorities);
    }

    /**
     * 清除关联关系
     *
     * @return mixed
     */
    public function then()
    {
        if (! $this->roles) {
            return Models::role()->resetAssigned($this->authority);
        }

        if (! ($roleIds = $this->getRoleIds())) {
            return false;
        }

        return $this->retractRoles($roleIds, [$this->authority]);
    }

    /**
     * Get the IDs of anyexisting roles provided.
     *
     * @return array
     */
    protected function getRoleIds()
    {
        $roles = Helpers::groupModelsAndIdentifiersByType($this->roles);

        $ids = [];
        if ($roles['integers']) {
            $ids = $roles['integers'];
        }

        if ($roles['models']) {
            foreach ($roles['models'] as $model) {
                $ids[] = $model->getId();
            }
        }

        if ($roles['strings']) {
            $ids = array_merge($ids, $this->getRoleIdsFromNames($roles['strings'])->all());
        }

        return $ids;
    }

    /**
     * Get the IDs of the roles with the given names.
     *
     * @param  string[]  $names
     * @return Collection
     */
    protected function getRoleIdsFromNames(array &$names)
    {
        $key = Models::role()->getKeyName();

        return (new Collection(
            Models::role()
            ->select($key)
            ->where('name', 'IN', $names)
            ->find()
        ))
            ->pluck($key);
    }

    /**
     * Retract the given roles from the given authorities.
     *
     * @param  array  $roleIds
     * @param  string $authorityClass
     * @param  array $authorityIds
     * @return mixed
     */
    protected function retractRoles($roleIds, array $authorities)
    {
        $query = $this->newPivotTableQuery();

        foreach ($roleIds as $roleId) {
            foreach ($authorities as $authority) {
                $query->orWhere([
                    'role_id' => $roleId,
                    'entity_id' => $authority->getId(),
                    'entity_type' => $authority->getMorphType(),
                ]);
            }
        }

        return $query->delete();
    }



    /**
     * Get a query builder instance for the assigned roles pivot table.
     *
     * @return Query
     */
    protected function newPivotTableQuery()
    {
        return query()->from(Models::table('assigned_roles'));
    }
}
