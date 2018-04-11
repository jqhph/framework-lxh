<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Clipboard;
use Lxh\MVC\Model;
use Lxh\Support\Collection;

class ChecksRoles
{
    /**
     * The authority against which to check for roles.
     *
     * @var Model
     */
    protected $authority;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * The bouncer clipboard instance.
     *
     * @var \Lxh\Auth\Clipboard
     */
    protected $clipboard;

    /**
     * Constructor.
     *
     * @param Model  $authority
     * @param \Lxh\Auth\Clipboard  $clipboard
     */
    public function __construct(Model $authority, Collection $roles, Clipboard $clipboard)
    {
        $this->authority = $authority;
        $this->roles     = $roles;
        $this->clipboard = $clipboard;
    }

    /**
     * Check if the authority has any of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function a($role)
    {
        $roles = func_get_args();

        return $this->clipboard->checkRole($roles, 'or');
    }

    /**
     * Check if the authority doesn't have any of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function notA($role)
    {
        $roles = func_get_args();

        return $this->clipboard->checkRole($roles, 'not');
    }

    /**
     * Alias to the "a" method.
     *
     * @param  string  $role
     * @return bool
     */
    public function an($role)
    {
        $roles = func_get_args();

        return $this->clipboard->checkRole($roles, 'or');
    }

    /**
     * Alias to the "notA" method.
     *
     * @param  string  $role
     * @return bool
     */
    public function notAn($role)
    {
        $roles = func_get_args();

        return $this->clipboard->checkRole($roles, 'not');
    }

    /**
     * Check if the authority has all of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function all($role)
    {
        $roles = func_get_args();

        return $this->clipboard->checkRole($roles, 'and');
    }
}
