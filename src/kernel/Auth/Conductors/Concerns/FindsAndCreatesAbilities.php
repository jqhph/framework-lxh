<?php

namespace Lxh\Auth\Conductors\Concerns;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Models;

use Lxh\Support\Arr;
use InvalidArgumentException;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

trait FindsAndCreatesAbilities
{
    /**
     * Get the IDs of the provided abilities.
     *
     * @param  Model|array|int  $abilities
     * @param  array  $attributes
     * @return array
     */
    protected function getAbilityIds($abilities, array $attributes = [])
    {
        if ($abilities instanceof Model) {
            return [$abilities->getId()];
        }

        if (Helpers::isAssociativeArray($abilities)) {
            return $this->getAbilityIdsFromMap($abilities, $attributes);
        }

        if (! is_array($abilities) && ! $abilities instanceof Collection) {
            $abilities = [$abilities];
        }

        return $this->getAbilityIdsFromArray($abilities, $attributes);
    }

    /**
     * Get the ability IDs for the given map.
     *
     * The map should use the ['ability-name' => Entity::class] format.
     *
     * @param  array  $map
     * @param  array  $attributes
     * @return array
     */
    protected function getAbilityIdsFromMap(array $map, array $attributes)
    {
        list($map, $list) = Helpers::partition($map, function ($value, $key) {
            return ! is_int($key);
        });

        return $map->map(function ($entity, $ability) use ($attributes) {
            return $this->getAbilityIds($ability, $entity, $attributes);
        })->collapse()->merge($this->getAbilityIdsFromArray($list, $attributes))->all();
    }

    /**
     * Get the ability IDs from the provided array, creating the ones that don't exist.
     *
     * @param  iterable  $abilities
     * @param  array  $attributes
     * @return array
     */
    protected function getAbilityIdsFromArray($abilities, array $attributes)
    {
        $groups = Helpers::groupModelsAndIdentifiersByType($abilities);

        $keyName = Models::ability()->getKeyName();

        $groups['strings'] = $this->abilitiesByName($groups['strings'], $attributes)
                                  ->pluck($keyName)->all();

        $groups['models'] = Arr::pluck($groups['models'], $keyName);

        return Arr::collapse($groups);
    }

    /**
     * Get or create abilities by their name.
     *
     * @param  array|string  $abilities
     * @param  array  $attributes
     * @return \Lxh\Support\Collection
     */
    protected function abilitiesByName($abilities, $attributes = [])
    {
        $abilities = array_unique(is_array($abilities) ? $abilities : [$abilities]);

        if (empty($abilities)) {
            return new Collection;
        }

        $existing = Models::ability()->simpleAbility()->whereIn('name', $abilities)->get();

        return $existing->merge($this->createMissingAbilities(
            $existing, $abilities, $attributes
        ));
    }

    /**
     * Create the non-existant abilities by name.
     *
     * @param  \Lxh\Database\Eloquent\Collection  $existing
     * @param  string[]  $abilities
     * @param  array  $attributes
     * @return array
     */
    protected function createMissingAbilities($existing, array $abilities, $attributes = [])
    {
        $missing = array_diff($abilities, $existing->pluck('name')->all());

        return array_map(function ($ability) use ($attributes) {
            return Models::ability()->create($attributes + ['name' => $ability]);
        }, $missing);
    }
}
