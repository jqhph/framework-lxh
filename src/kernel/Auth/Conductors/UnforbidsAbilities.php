<?php

namespace Lxh\Auth\Conductors;

class UnforbidsAbilities
{
    use Concerns\DisassociatesAbilities;

    /**
     * The authority from which to remove the forbiddal.
     *
     * @var \Lxh\Database\Eloquent\Model|string
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
     * @param \Lxh\Database\Eloquent\Model|string  $authority
     */
    public function __construct($authority)
    {
        $this->authority = $authority;
    }
}
