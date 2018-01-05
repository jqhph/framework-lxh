<?php

namespace Lxh\Auth\Conductors\Concerns;

use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

trait AssociatesAbilities
{
    use ConductsAbilities, FindsAndCreatesAbilities;

    /**
     * Get the authority, creating a role authority if necessary.
     *
     * @return Model
     */
    protected function getAuthority()
    {
        if ($this->authority instanceof Model) {
            return $this->authority;
        }

        return Models::role()->firstOrCreate(['name' => $this->authority]);
    }

    /**
     * Get the IDs of the associated abilities.
     *
     * @param  Model  $authority
     * @param  array  $abilityIds
     * @param  bool $forbidden
     * @return array
     */
    protected function getAssociatedAbilityIds(Model $authority, array $abilityIds, $forbidden)
    {
        $relation = $authority->abilities();

        $relation->where('id', 'IN', $abilityIds)->wherePivot('forbidden', '=', $forbidden);

        return $relation->get(['id'])->pluck('id')->all();
    }
}
