<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Models;
use Lxh\Support\Arr;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

trait IsRole
{
    use HasAbilities, Authorizable {
        HasAbilities::getClipboardInstance insteadof Authorizable;
    }

    /**
     * The users relationship.
     *
     * @return array
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

}
