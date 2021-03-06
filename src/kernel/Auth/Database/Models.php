<?php

namespace Lxh\Auth\Database;

use Closure;
use Lxh\Admin\Models\Admin as AppAdmin;
use Lxh\Container\Container;
use Lxh\Mvc\Model;

class Models
{
    /**
     * The prefix for the tables.
     *
     * @var string
     */
    protected static $prefix = '';

    /**
     * Map for the bouncer's models.
     *
     * @var array
     */
    protected static $models = [
        'User'    => AppAdmin::class,
        'Role'    => Role::class,
        'Ability' => Ability::class,
    ];

    /**
     * Holds the map of ownership for models.
     *
     * @var array
     */
    protected static $ownership = [];

    /**
     * Map for the bouncer's tables.
     *
     * @var array
     */
    protected static $tables = [
        'role'               => 'roles',
        'ability'            => 'abilities',
        'assigned_roles'     => 'assigned_roles',
        'assigned_abilities' => 'assigned_abilities'
    ];

    /**
     * @var array
     */
    protected static $keyNames = [
        'Role'    => 'id',
        'Ability' => 'id',
        'User'    => 'id',
        'Menu'    => 'id',
    ];

    /**
     * Set the model to be used for abilities.
     *
     * @param  string  $model
     * @return void
     */
    public static function setAbilitiesModel($model)
    {
        static::$models['Ability'] = $model;
    }

    /**
     * @param $model
     * @param $keyName
     */
    public static function setKeyName($model, $keyName)
    {
        static::$keyNames[$model] = $keyName;
    }

    /**
     * @param $model
     * @return mixed
     */
    public static function getKeyName($model)
    {
        return getvalue(static::$keyNames, $model);
    }

    /**
     * @return mixed
     */
    public static function getUserKeyName()
    {
        return static::getKeyName('User');
    }


    /**
     * @return mixed
     */
    public static function getRoleKeyName()
    {
        return static::getKeyName('Role');
    }


    /**
     * @return mixed
     */
    public static function getAbilityKeyName()
    {
        return static::getKeyName('Ability');
    }

    /**
     * @return mixed
     */
    public static function getMenuKeyName()
    {
        return static::getKeyName('Menu');
    }

    /**
     * Set the model to be used for roles.
     *
     * @param  string  $model
     * @return void
     */
    public static function setRolesModel($model)
    {
        static::$models['Role'] = $model;
    }

    /**
     * Set the model to be used for users.
     *
     * @param  string  $model
     * @return void
     */
    public static function setUsersModel($model)
    {
        static::$models['User'] = $model;

        static::$tables['users'] = static::user()->getTableName();
    }

    /**
     * Set custom table names.
     *
     * @param  array  $map
     * @return void
     */
    public static function setTables(array $map)
    {
        static::$tables = array_merge(static::$tables, $map);
    }

    /**
     * Set the prefix for the tables.
     *
     * @param  string  $prefix
     * @return void
     */
    public static function setPrefix($prefix)
    {
        static::$prefix = $prefix;
    }

    /**
     * Get a custom table name mapping for the given table.
     *
     * @param  string  $table
     * @return string
     */
    public static function table($table)
    {
        if (isset(static::$tables[$table])) {
            return static::$tables[$table];
        }

        return $table;
    }

    /**
     * Get the prefix for the tables.
     *
     * @return string
     */
    public static function prefix()
    {
        return static::$prefix;
    }

    /**
     * Get the classname mapping for the given model.
     *
     * @param  string  $model
     * @return string
     */
    public static function classname($model)
    {
        if (isset(static::$models[$model])) {
            return static::$models[$model];
        }

        return $model;
    }

    /**
     * Get an instance of the ability model.
     *
     * @param  array  $attributes
     * @return \Lxh\Auth\Database\Ability
     */
    public static function ability(array $attributes = [])
    {
        return static::make('Ability')->attach($attributes);;
    }

    /**
     * Get an instance of the role model.
     *
     * @param  array  $attributes
     * @return \Lxh\Auth\Database\Role
     */
    public static function role(array $attributes = [])
    {
        return static::make('Role')->attach($attributes);
    }

    /**
     * @param $class
     * @return Model
     */
    public static function make($model)
    {
        $class = static::classname($model);

        return new $class();
    }

    /**
     * Get an instance of the user model.
     *
     * @param  array  $attributes
     * @return Model
     */
    public static function user(array $attributes = [])
    {
        return static::make('User')->attach($attributes);
    }


    /**
     * Reset all settings to their original state.
     *
     * @return void
     */
    public static function reset()
    {
        static::$models = static::$tables = [];
    }

    /**
     * Get the basename of the given class.
     *
     * @param  string|object  $class
     * @return string
     */
    protected static function basename($class)
    {
        if ( ! is_string($class)) {
            $class = get_class($class);
        }

        $segments = explode('\\', $class);

        return end($segments);
    }
}
