<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

class GivesAbilities
{
    use Concerns\AssociatesAbilities;

    /**
     * The authority to be given abilities.
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
     * Give the abilities to the authority.
     *
     * @param  Model|array|int  $abilities
     * @param  Model|string|null  $model
     * @param  array  $attributes
     * @return void
     */
    public function then($abilities, array $attributes = [])
    {
    }

    /**
     * Associate the given ability IDs as allowed abilities.
     *
     * @param  array  $ids
     * @param  Model  $authority
     * @return mixed
     */
    protected function giveAbilities(array $ids, Model $authority)
    {
    }
}
