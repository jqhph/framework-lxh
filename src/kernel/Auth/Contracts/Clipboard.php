<?php

namespace Lxh\Bouncer\Contracts;

use \Lxh\MVC\Model;
use Lxh\Support\Collection;

interface Clipboard
{
    /**
     * Determine if the given authority has the given ability.
     *
     * @param  \Lxh\MVC\Model  $authority
     * @param  string  $ability
     * @param  \Lxh\MVC\Model|string|null  $model
     * @return bool
     */
    public function check(Model $authority, $ability, $model = null);

    /**
     * Check if an authority has the given roles.
     *
     * @param  \Lxh\MVC\Model  $authority
     * @param  array|string  $roles
     * @param  string  $boolean
     * @return bool
     */
    public function checkRole(Model $authority, $roles, $boolean = 'or');

    /**
     * Get the given authority's roles.
     *
     * @param  \Lxh\MVC\Model  $authority
     * @return \Lxh\Support\Collection
     */
    public function getRoles(Model $authority);

    /**
     * Get a list of the authority's abilities.
     *
     * @param  \Lxh\MVC\Model  $authority
     * @param  bool  $allowed
     * @return Collection
     */
    public function getAbilities(Model $authority);

    /**
     * Get a list of the authority's forbidden abilities.
     *
     * @param  \Lxh\MVC\Model  $authority
     * @return Collection
     */
    public function getForbiddenAbilities(Model $authority);
}
