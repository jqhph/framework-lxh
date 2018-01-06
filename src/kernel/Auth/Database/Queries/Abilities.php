<?php

namespace Lxh\Auth\Database\Queries;

use Lxh\Auth\Database\Models;

use Lxh\Support\Collection;
use Lxh\MVC\Model;

class Abilities
{
    /**
     * Get a list of the authority's abilities.
     *
     * @param  Model  $authority
     * @param  bool  $allowed
     * @return Collection
     */
    public function getForAuthority(Model $authority)
    {
        Models::ability()->where()->find();

        return Models::ability()
                     ->whereExists($this->getRoleConstraint($authority))
                     ->orWhereExists($this->getAuthorityConstraint($authority))
                     ->get();
    }

    /**
     * Get a constraint for abilities that have been granted to the given authority through a role.
     *
     * @param  Model  $authority
     * @param  bool  $allowed
     * @return \Closure
     */
    protected function getRoleConstraint(Model $authority)
    {
        $entityType = $authority->getMorphType();
        $id = $authority->getId();

        $assigned = Models::table('assigned_roles');
        $abilities   = Models::table('abilities');
        $roles       = Models::table('roles');

        $i = query()->from($abilities)
            ->where(['entity_id' => $id, 'entity_type' => $entityType])
            ->find();

        $i = query()->from('role');

        return function ($query) use ($authority) {
            $permissions = Models::table('permissions');
            $abilities   = Models::table('abilities');
            $roles       = Models::table('roles');
            $prefix      = Models::prefix();

            $query->from($roles)
                  ->join($permissions, $roles.'.id', '=', $permissions.'.entity_id')
                  ->whereRaw("{$prefix}{$permissions}.ability_id = {$prefix}{$abilities}.id")
                  ->where($permissions.".entity_type", Models::role()->getMorphClass());

            Models::scope()->applyToModelQuery($query, $roles);
            Models::scope()->applyToRelationQuery($query, $permissions);

            $query->where(function ($query) use ($roles, $authority) {
                $query->whereExists($this->getAuthorityRoleConstraint($authority));

            });
        };
    }


    /**
     * Get a constraint for roles that are assigned to the given authority.
     *
     * @param  Model  $authority
     * @return \Closure
     */
    protected function getAuthorityRoleConstraint(Model $authority)
    {
        return function ($query) use ($authority) {
            $pivot  = Models::table('assigned_roles');
            $roles  = Models::table('roles');
            $table  = $authority->getTable();
            $prefix = Models::prefix();

            $query->from($table)
                  ->join($pivot, "{$table}.{$authority->getKeyName()}", '=', $pivot.'.entity_id')
                  ->whereRaw("{$prefix}{$pivot}.role_id = {$prefix}{$roles}.id")
                  ->where($pivot.'.entity_type', $authority->getMorphClass())
                  ->where("{$table}.{$authority->getKeyName()}", $authority->getId());

            Models::scope()->applyToModelQuery($query, $roles);
            Models::scope()->applyToRelationQuery($query, $pivot);
        };
    }

    /**
     * Get a constraint for abilities that have been granted to the given authority.
     *
     * @param  Model  $authority
     * @param  bool  $allowed
     * @return \Closure
     */
    protected function getAuthorityConstraint(Model $authority, $allowed)
    {
        return function ($query) use ($authority, $allowed) {
            $permissions = Models::table('permissions');
            $abilities   = Models::table('abilities');
            $table       = $authority->getTable();
            $prefix      = Models::prefix();

            $query->from($table)
                  ->join($permissions, "{$table}.{$authority->getKeyName()}", '=', $permissions.'.entity_id')
                  ->whereRaw("{$prefix}{$permissions}.ability_id = {$prefix}{$abilities}.id")
                  ->where("{$permissions}.entity_type", $authority->getMorphClass())
                  ->where("{$permissions}.forbidden", ! $allowed)
                  ->where("{$table}.{$authority->getKeyName()}", $authority->getId());

            Models::scope()->applyToModelQuery($query, $abilities);
            Models::scope()->applyToRelationQuery($query, $permissions);
        };
    }
}
