<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\Clipboard;
use Lxh\Database\Eloquent\Model;

class ChecksRoles
{
    /**
     * The authority against which to check for roles.
     *
     * @var \Lxh\Database\Eloquent\Model
     */
    protected $authority;

    /**
     * The bouncer clipboard instance.
     *
     * @var \Lxh\Auth\Clipboard
     */
    protected $clipboard;

    /**
     * Constructor.
     *
     * @param \Lxh\Database\Eloquent\Model  $authority
     * @param \Lxh\Auth\Clipboard  $clipboard
     */
    public function __construct(Model $authority, Clipboard $clipboard)
    {
        $this->authority = $authority;
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

        return $this->clipboard->checkRole($this->authority, $roles, 'or');
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

        return $this->clipboard->checkRole($this->authority, $roles, 'not');
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

        return $this->clipboard->checkRole($this->authority, $roles, 'or');
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

        return $this->clipboard->checkRole($this->authority, $roles, 'not');
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

        return $this->clipboard->checkRole($this->authority, $roles, 'and');
    }
}
