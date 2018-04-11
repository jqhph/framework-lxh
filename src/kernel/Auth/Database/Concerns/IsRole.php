<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Models;
use Lxh\Support\Arr;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

trait IsRole
{
    /**
     * @var Collection
     */
    protected $abilities;

    /**
     * The users relationship.
     *
     * @return array
     */
    public function users()
    {
    }

    /**
     * Get all of the model's allowed abilities.
     *
     * @return Collection
     */
    public function getAbilities()
    {
        if ($this->abilities)
            return $this->abilities;

        $id = $this->getId();

        if (! $id)
            return $this->abilities = new Collection();

        $id = [$id];

        return $this->abilities = new Collection(
            Models::ability()->findForRolesIds($id, $this->getMorphType())
        );

    }

    /**
     * Get all of the model's allowed abilities.
     *
     * @return Collection
     */
    public function getForbiddenAbilities()
    {
        return $this->getAbilities()->filter(function (&$row) {
            return get_value($row, 'forbidden');
        });
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
        if (! $id = $this->getId()) {
            return $this;
        }

        list($model, $keys) = Helpers::extractModelAndKeys($model, $keys);

        $type = $model->getMorphType();

        $inserts = [];
        foreach ($keys as &$key) {
            $inserts[] = [
                'role_id'     => &$id,
                'entity_id'   => $key,
                'entity_type' => &$type,
            ];
        }

        $this->query()
            ->from(Models::table('assigned_roles'))
            ->batchReplace($inserts);

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

        foreach ($roles['models'] as $model) {
            if ($id = $model->getId()) {
                $roles['integers'][] = $id;
            } elseif ($slug = $model->get('slug')) {
                $roles['strings'][] = $slug;
            }
        }
        unset($roles['models']);
        
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
            $this->where('slug', 'IN', $names)->select($key)->find()
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
            $this->where($this->getKeyName(), 'IN', $keys)->select('slug')->find()
        ))->pluck('slug')->all();
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

        $query = query($this->connectionKeyName)
            ->from($table = Models::table('assigned_roles'))
            ->where('role_id', $this->getId())
            ->where('entity_type', $model->getMorphType())
            ->where('entity_id', 'IN', $keys);

        $query->delete();

        return $this;
    }

}
