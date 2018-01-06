<?php

namespace Lxh\Bouncer\Contracts;

use \Lxh\MVC\Model;
use Lxh\Support\Collection;

interface Clipboard
{
    /**
     * Determine if the given authority has the given ability.
     *
     * @param  string  $ability
     * @param  \Lxh\MVC\Model|string|null  $model
     * @return bool
     */
    public function check($ability, $model = null);

    /**
     * Check if an authority has the given roles.
     *
     * @param  array|string  $roles
     * @param  string  $boolean
     * @return bool
     */
    public function checkRole($roles, $boolean = 'or');

    /**
     * Get the given authority's roles.
     *
     * @return \Lxh\Support\Collection
     */
    public function getRoles();

    /**
     * Get a list of the authority's abilities.
     *
     * @return Collection
     */
    public function getAbilities();

    /**
     * Get a list of the authority's forbidden abilities.
     *
     * @return Collection
     */
    public function getForbiddenAbilities();
}
