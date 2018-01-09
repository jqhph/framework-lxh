<?php

namespace Lxh\Auth\Conductors;

use Lxh\MVC\Model;

class UnforbidsAbilities
{
    /**
     * The authority from which to remove the forbiddal.
     *
     * @var Model|string
     */
    protected $authority;

    /**
     * The constraints to use for the detach abilities query.
     *
     * @var array
     */
    protected $constraints = ['forbidden' => true];

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
