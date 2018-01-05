<?php

namespace Lxh\Auth\Conductors\Concerns;

use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Ability;

use InvalidArgumentException;
use Lxh\Database\Eloquent\Model;
use Lxh\Database\Eloquent\Collection;
use Lxh\Database\Eloquent\Relations\MorphToMany;

trait DisassociatesAbilities
{
    use ConductsAbilities, FindsAndCreatesAbilities;

    /**
     * Remove the given ability from the model.
     *
     * @param  mixed  $abilities
     * @param  \Lxh\Database\Eloquent\Model|string|null  $entity
     * @param  array  $attributes
     * @return bool
     */
    public function to($abilities, $entity = null, array $attributes = [])
    {
        if (call_user_func_array([$this, 'shouldConductLazy'], func_get_args())) {
            return $this->conductLazy($abilities);
        }

        $authority = $this->getAuthority();

        if ($ids = $this->getAbilityIds($abilities, $entity, $attributes)) {
            $this->detachAbilities($authority, $ids);
        }

        return true;
    }

    /**
     * Detach the given IDs from the model.
     *
     * @param  \Lxh\Database\Eloquent\Model  $model
     * @param  array  $ids
     * @return void
     */
    protected function detachAbilities(Model $model, $ids)
    {
        $constraints = property_exists($this, 'constraints') ? $this->constraints : [];

        $this->getAbilitiesPivotQuery($model, $ids)
             ->where($this->constraints)
             ->delete();
    }

    /**
     * Get the base abilities pivot query.
     *
     * @param  \Lxh\Database\Eloquent\Model  $model
     * @param  array  $ids
     * @return \Lxh\Database\Query\Builder
     */
    protected function getAbilitiesPivotQuery(Model $model, $ids)
    {
        $relation = $model->abilities();

        list($foreignKeyName, $relatedKeyName) = $this->getRelationKeyNames($relation);

        return $relation->newPivotStatement()
                        ->where($foreignKeyName, $model->getId())
                        ->whereIn($relatedKeyName, $ids);
    }

    /**
     * Get the two primary key names from the relation.
     *
     * @param  Lxh\Database\Eloquent\Relations\MorphToMany  $relation
     * @return array
     */
    protected function getRelationKeyNames(MorphToMany $relation)
    {
        // We need to get the keys of both tables from the relation class.
        // The method names have changed in Laravel 5.4 & again in 5.5,
        // so we will first check which methods are available to us.
        if (method_exists($relation, 'getForeignKey')) {
            return [
                $relation->getForeignKey(),
                $relation->getOtherKey(),
            ];
        }

        if (method_exists($relation, 'getQualifiedForeignKeyName')) {
            return [
                $relation->getQualifiedForeignKeyName(),
                $relation->getQualifiedRelatedKeyName(),
            ];
        }

        return [
            $relation->getQualifiedForeignPivotKeyName(),
            $relation->getQualifiedRelatedPivotKeyName(),
        ];
    }

    /**
     * Get the authority from which to disassociate the abilities.
     *
     * @return \Lxh\Database\Eloquent\Model
     */
    protected function getAuthority()
    {
        if ($this->authority instanceof Model) {
            return $this->authority;
        }

        return Models::role()->where('name', $this->authority)->firstOrFail();
    }
}
