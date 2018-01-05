<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Database\Models;
use Lxh\Database\Eloquent\Model;

class ForbidsAbilities
{
    use Concerns\AssociatesAbilities;

    /**
     * The authority to be forbidden from the abilities.
     *
     * @var \Lxh\Database\Eloquent\Model|string
     */
    protected $authority;

    /**
     * Constructor.
     *
     * @param \Lxh\Database\Eloquent\Model|string  $authority
     */
    public function __construct($authority)
    {
        $this->authority = $authority;
    }

    /**
     * Forbid the abilities to the authority.
     *
     * @param  mixed  $abilities
     * @param  \Lxh\Database\Eloquent\Model|string|null  $model
     * @param  array  $attributes
     * @return bool
     */
    public function to($abilities, $model = null, array $attributes = [])
    {
        if (call_user_func_array([$this, 'shouldConductLazy'], func_get_args())) {
            return $this->conductLazy($abilities);
        }

        $ids = $this->getAbilityIds($abilities, $model, $attributes);

        $this->forbidAbilities($ids, $this->getAuthority());

        return true;
    }

    /**
     * Associate the given abilitiy IDs as forbidden abilities.
     *
     * @param  array  $ids
     * @param  \Lxh\Database\Eloquent\Model  $authority
     * @return void
     */
    protected function forbidAbilities(array $ids, Model $authority)
    {
        $ids = array_diff($ids, $this->getAssociatedAbilityIds($authority, $ids, true));

        $attributes = Models::scope()->getAttachAttributes(get_class($authority));

        $authority->abilities()->attach($ids, $attributes + ['forbidden' => true]);
    }
}
