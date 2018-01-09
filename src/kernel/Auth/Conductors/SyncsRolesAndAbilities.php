<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Helpers;
use Lxh\Auth\Database\Models;

use Lxh\Support\Arr;
use Lxh\Support\Collection;
use Lxh\MVC\Model;

class SyncsRolesAndAbilities
{
    /**
     * The authority for whom to sync roles/abilities.
     *
     * @var Model|string
     */
    protected $authority;

    /**
     * Constructor.
     *
     * @param Model|string  $authority
     */
    public function __construct($authority)
    {
        $this->authority = $authority;
    }

    /**
     * Sync the provided roles to the authority.
     *
     * @param  iterable  $roles
     * @return void
     */
    public function roles($roles)
    {
    }

    /**
     * Sync the provided abilities to the authority.
     *
     * @param  iterable  $abilities
     * @return void
     */
    public function abilities($abilities)
    {
    }

    /**
     * Sync the provided forbidden abilities to the authority.
     *
     * @param  iterable  $abilities
     * @return void
     */
    public function forbiddenAbilities($abilities)
    {
    }

    /**
     * Sync the given abilities for the authority.
     *
     * @param  iterable  $abilities
     * @param  array  $options
     * @return void
     */
    protected function syncAbilities($abilities, $options = ['forbidden' => false])
    {
    }

    /**
     * Get the authority for whom to sync roles/abilities.
     *
     * @return Model
     */
    protected function getAuthority()
    {
    }

    /**
     * Get the fully qualified column name for the abilities table's primary key.
     *
     * @return string
     */
    protected function getAbilitiesQualifiedKeyName()
    {
    }
}
