<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

class ForbidsAbilities
{
    /**
     * The authority to be forbidden from the abilities.
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
     * Forbid the abilities to the authority.
     *
     * @param  mixed  $abilities
     * @param  Model|string|null  $model
     * @param  array  $attributes
     * @return bool
     */
    public function then($abilities, $model = null, array $attributes = [])
    {
    }

    /**
     * Associate the given abilitiy IDs as forbidden abilities.
     *
     * @param  array  $ids
     * @param  Model  $authority
     * @return void
     */
    protected function forbidAbilities(array $ids, Model $authority)
    {
    }
}
