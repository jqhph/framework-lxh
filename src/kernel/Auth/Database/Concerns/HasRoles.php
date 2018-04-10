<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Container\Container;

use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Role;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Conductors\AssignsRoles;
use Lxh\Auth\Conductors\RemovesRoles;

trait HasRoles
{
    /**
     * The roles relationship.
     *
     * @return array
     */
    public function roles()
    {
        $relation = $this->morphToMany(
            Models::classname(Role::class),
            'entity',
            Models::table('assigned_roles')
        );

        return Models::scope()->applyToRelation($relation);
    }

    /**
     * Check if the model has any of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function isAn($role)
    {
        $roles = func_get_args();

        $clipboard = $this->getClipboardInstance();

        return $clipboard->checkRole($this, $roles, 'or');
    }

    /**
     * Check if the model has any of the given roles.
     *
     * Alias for the "isAn" method.
     *
     * @param  string  $role
     * @return bool
     */
    public function isA($role)
    {
        return call_user_func_array([$this, 'isAn'], func_get_args());
    }

    /**
     * Check if the model has none of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function isNotAn($role)
    {
        $roles = func_get_args();

        $clipboard = $this->getClipboardInstance();

        return $clipboard->checkRole($this, $roles, 'not');
    }

    /**
     * Check if the model has none of the given roles.
     *
     * Alias for the "isNotAn" method.
     *
     * @param  string  $role
     * @return bool
     */
    public function isNotA($role)
    {
        return call_user_func_array([$this, 'isNotAn'], func_get_args());
    }

    /**
     * Check if the model has all of the given roles.
     *
     * @param  string  $role
     * @return bool
     */
    public function isAll($role)
    {
        $roles = func_get_args();

        $clipboard = $this->getClipboardInstance();

        return $clipboard->checkRole($this, $roles, 'and');
    }
    
    /**
     * Get an instance of the bouncer's clipboard.
     *
     * @return \Lxh\Auth\Clipboard
     */
    protected function getClipboardInstance()
    {
        $container = Container::getInstance() ?: new Container;

        return $container->make(Clipboard::class);
    }
}
