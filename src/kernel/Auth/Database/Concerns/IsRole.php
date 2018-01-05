<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Scope\BaseTenantScope;
use Lxh\Auth\Database\Queries\Roles as RolesQuery;

use App\User;
use Lxh\ORM\Connect\Mongo\Query;
use Lxh\Support\Arr;
use InvalidArgumentException;
use Lxh\Support\Collection;
use Lxh\Database\Eloquent\Model;

trait IsRole
{
    use HasAbilities, Authorizable {
        HasAbilities::getClipboardInstance insteadof Authorizable;
    }

    /**
     * Boot the is role trait.
     *
     * @return void
     */
    public static function bootIsRole()
    {
        BaseTenantScope::register(static::class);

        static::creating(function ($role) {
            Models::scope()->applyToModel($role);
        });
    }

    /**
     * The users relationship.
     *
     * @return \Lxh\Database\Eloquent\Relations\MorphedByMany
     */
    public function users()
    {
        $relation = $this->morphedByMany(
            Models::classname(User::class),
            'entity',
            Models::table('assigned_roles')
        );

        return Models::scope()->applyToRelation($relation);
    }

    /**
     * Assign the role to the given model(s).
     *
     * @param  string|Model|Collection  $model
     * @param  array|null  $keys
     * @return $this
     */
    public function assignTo($model, array $keys = null)
    {
        list($model, $keys) = Helpers::extractModelAndKeys($model, $keys);

        $query = $this->newBaseQueryBuilder()->from(Models::table('assigned_roles'));

        $query->insert($this->createAssignRecords($model, $keys));

        return $this;
    }

    /**
     * Find the given roles, creating the names that don't exist yet.
     *
     * @param  iterable  $roles
     * @return Collection
     */
    public function findOrCreateRoles($roles)
    {
        $roles = Helpers::groupModelsAndIdentifiersByType($roles);

        if ($roles['integers']) {
            $roles['integers'] = $this->where('id', 'IN', $roles['integers'])->find();
        }


        $roles['strings'] = $this->findOrCreateRolesByName($roles['strings']);

        return new Collection(Arr::collapse($roles));
    }

    /**
     * Find roles by name, creating the ones that don't exist.
     *
     * @param  iterable  $names
     * @return Collection
     */
    protected function findOrCreateRolesByName($names)
    {
        if (empty($names)) {
            return [];
        }

        $existing = (new Collection($this->where('name', 'IN', $names)->find()))->keyBy('name');

        return (new Collection($names))
                ->diff($existing->pluck('name'))
                ->map(function ($name) {
                    return static::create(compact('name'));
                })
                ->merge($existing);
    }

    /**
     * Get the IDs of the given roles.
     *
     * @param  iterable  $roles
     * @return array
     */
    public function getRoleKeys($roles)
    {
        $roles = Helpers::groupModelsAndIdentifiersByType($roles);

        if ($roles['strings']) {
            $roles['strings'] = $this->getKeysByName($roles['strings']);
        }

        if ($roles['models']) {
            $roles['models'] = Arr::pluck($roles['models'], $this->getKeyName());
        }

        return $roles ? Arr::collapse($roles) : [];
    }

    /**
     * Get the names of the given roles.
     *
     * @param  iterable  $roles
     * @return array
     */
    public function getRoleNames($roles)
    {
        $roles = Helpers::groupModelsAndIdentifiersByType($roles);

        $roles['integers'] = $this->getNamesByKey($roles['integers']);

        $roles['models'] = Arr::pluck($roles['models'], 'name');

        return Arr::collapse($roles);
    }

    /**
     * Get the keys of the roles with the given names.
     *
     * @param  iterable  $names
     * @return array
     */
    public function getKeysByName($names)
    {
        if (empty($names)) {
            return [];
        }

        $key = $this->getKeyName();

        return (new Collection(
            $this->where('name', 'IN', $names)->select($key)->find()
        ))->pluck($key)->all();
    }

    /**
     * Get the names of the roles with the given IDs.
     *
     * @param  iterable  $keys
     * @return array
     */
    public function getNamesByKey($keys)
    {
        if (empty($keys)) {
            return [];
        }

        return (new Collection(
            $this->where($this->getKeyName(), 'IN', $keys)->select('name')->find()
        ))->pluck('name')->all();
    }

    /**
     * Retract the role from the given model(s).
     *
     * @param  string|Model|Collection  $model
     * @param  array|null  $keys
     * @return $this
     */
    public function retractFrom($model, array $keys = null)
    {
        list($model, $keys) = Helpers::extractModelAndKeys($model, $keys);

        $query = query()
            ->from($table = Models::table('assigned_roles'))
            ->where('role_id', $this->getId())
            ->where('entity_type', $model->getMorphType())
            ->where('entity_id', 'IN', $keys);

        $query->delete();

        return $this;
    }

    /**
     * Create the pivot table records for assigning the role to given models.
     *
     * @param  Model  $model
     * @param  array  $keys
     * @return array
     */
    protected function createAssignRecords(Model $model, array $keys)
    {
        $type = $model->getMorphType();

        return array_map(function ($key) use ($type) {
            return Models::scope()->getAttachAttributes() + [
                'role_id'     => $this->getId(),
                'entity_type' => $type,
                'entity_id'   => $key,
            ];
        }, $keys);
    }

    /**
     * Constrain the given query to roles that were assigned to the given authorities.
     *
     * @param  Query  $query
     * @param  string|Model|Collection  $model
     * @param  array  $keys
     * @return void
     */
    public function scopeWhereAssignedTo($query, $model, array $keys = null)
    {
        (new RolesQuery)->constrainWhereAssignedTo($query, $model, $keys);
    }
}
