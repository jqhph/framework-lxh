<?php

namespace Lxh\Auth\Conductors;

use Lxh\MVC\Model;

class RemovesAbilities
{
    /**
     * The authority from which to remove abilities.
     *
     * @var Model|string
     */
    protected $authority;

    /**
     * The constraints to use for the detach abilities query.
     *
     * @var array
     */
    protected $constraints = ['forbidden' => false];

    /**
     * Constructor.
     *
     * @param Model|string  $authority
     */
    public function __construct($authority)
    {
        $this->authority = $authority;
    }
}
