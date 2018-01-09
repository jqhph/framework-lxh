<?php

namespace Lxh\Auth;

use Lxh\Auth\Access\HandlesAuthorization;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Queries\Abilities as AbilitiesQuery;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

class Clipboard
{
    use HandlesAuthorization;

    /**
     * @var Model
     */
    protected $user;

    public function __construct(Model $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the given authority has the given ability.
     *
     * @param  Model  $authority
     * @param  string  $ability
     * @param  Model|string|null  $model
     * @return bool
     */
    public function check($ability, $model = null)
    {
        return (bool) $this->checkGetId($ability, $model);
    }

    /**
     * Determine if the given authority has the given ability, and return the ability ID.
     *
     * @param  Model  $authority
     * @param  string  $ability
     * @param  Model|string|null  $model
     * @return int|bool|null
     */
    protected function checkGetId($ability, $model = null)
    {
        $applicable = $this->compileAbilityIdentifiers($ability, $model);

        // We will first check if any of the applicable abilities have been forbidden.
        // If so, we'll return false right away, so as to not pass the check. Then,
        // we'll check if any of them have been allowed & return the matched ID.
        $forbiddenId = $this->findMatchingAbility(
            $this->getForbiddenAbilities(), $applicable, $model
        );

        if ($forbiddenId) {
            return false;
        }

        return $this->findMatchingAbility(
            $this->getAbilities(), $applicable, $model
        );
    }

    /**
     * Determine if any of the abilities can be matched against the provided applicable ones.
     *
     * @param  \Lxh\Support\Collection  $abilities
     * @param  \Lxh\Support\Collection  $applicable
     * @param  Model  $model
     * @param  Model  $authority
     * @return int|null
     */
    protected function findMatchingAbility($abilities, $applicable, $model)
    {
        $abilities = $abilities->toBase()->pluck('identifier', 'id');

        if ($id = $this->getMatchedAbilityId($abilities, $applicable)) {
            return $id;
        }

        if ($model instanceof Model && Models::isOwnedBy($authority, $model)) {
            return $this->getMatchedAbilityId($abilities, $applicable->map(function ($identifier) {
                return $identifier.'-owned';
            }));
        }
    }

    /**
     * Get the ID of the ability that matches one of the applicable abilities.
     *
     * @param  \Lxh\Support\Collection  $abilityMap
     * @param  \Lxh\Support\Collection  $applicable
     * @return int|null
     */
    protected function getMatchedAbilityId(Collection $abilityMap, Collection $applicable)
    {
        foreach ($abilityMap as $id => $identifier) {
            if ($applicable->contains($identifier)) {
                return $id;
            }
        }
    }

    /**
     * Check if an authority has the given roles.
     *
     * @param  Model  $authority
     * @param  array|string  $roles
     * @param  string  $boolean
     * @return bool
     */
    public function checkRole($roles, $boolean = 'or')
    {
        $available = $this->getRoles()
                          ->intersect(Models::role()
                          ->getRoleNames($roles));

        if ($boolean == 'or') {
            return $available->count() > 0;
        } elseif ($boolean === 'not') {
            return $available->count() === 0;
        }

        return $available->count() == count((array) $roles);
    }

    /**
     * Compile a list of ability identifiers that match the provided parameters.
     *
     * @param  string  $ability
     * @param  Model|string|null  $model
     * @return \Lxh\Support\Collection
     */
    protected function compileAbilityIdentifiers($ability, $model)
    {
        $ability = strtolower($ability);

        if (is_null($model)) {
            return new Collection([$ability, '*-*', '*']);
        }

        return new Collection($this->compileModelAbilityIdentifiers($ability, $model));
    }

    /**
     * Compile a list of ability identifiers that match the given model.
     *
     * @param  string  $ability
     * @param  string  $model
     * @return array
     */
    protected function compileModelAbilityIdentifiers($ability, $model)
    {
    }

    /**
     * Get the given authority's roles.
     *
     * @return \Lxh\Support\Collection
     */
    public function getRoles()
    {
        $collection = $this->user->roles()->get(['name'])->pluck('name');

        // In Laravel 5.1, "pluck" returns an Eloquent collection,
        // so we call "toBase" on it. In 5.2, "pluck" returns a
        // base instance, so there is no "toBase" available.
        if (method_exists($collection, 'toBase')) {
            $collection = $collection->toBase();
        }

        return $collection;
    }

    /**
     * Get a list of the authority's abilities.
     *
     * @param  Model  $authority
     * @return Collection
     */
    public function getAbilities()
    {
        $abilities = Models::ability()->getForAuthority($this->user);

        return new Collection($this->formatArray($abilities));
    }

    protected function formatArray(array &$abilities)
    {
        $content = [];
        foreach ($abilities as &$row) {
            $content[$row['name']] = $row;
        }
        return $content;
    }

    /**
     * Get a list of the authority's forbidden abilities.
     *
     * @param  Model  $authority
     * @return Collection
     */
    public function getForbiddenAbilities()
    {
        return $this->getAbilities();
    }
}
