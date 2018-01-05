<?php

namespace Lxh\Auth\Database;

use Closure;
use Lxh\Admin\Models\Admin;
use Lxh\Container\Container;
use Lxh\MVC\Model;
use Lxh\Auth\Database\Scope\Scope;

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
    protected static $models = [];

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
        'roles' => 'role',
        'abilities' => 'abilities',
    ];

    /**
     * The model scoping instance.
     *
     * @var \Lxh\Auth\Database\Scope\Scope
     */
    protected static $scope;

    /**
     * Set the model to be used for abilities.
     *
     * @param  string  $model
     * @return void
     */
    public static function setAbilitiesModel($model)
    {
        static::$models[Ability::class] = $model;
    }

    /**
     * Set the model to be used for roles.
     *
     * @param  string  $model
     * @return void
     */
    public static function setRolesModel($model)
    {
        static::$models[Role::class] = $model;
    }

    /**
     * Set the model to be used for users.
     *
     * @param  string  $model
     * @return void
     */
    public static function setUsersModel($model)
    {
        static::$models[Admin::class] = $model;

        static::$tables['users'] = static::user()->getTable();
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
     * Register an attribute/callback to determine if a model is owned by a given authority.
     *
     * @param  string|\Closure  $model
     * @param  string|\Closure|null  $attribute
     * @return void
     */
    public static function ownedVia($model, $attribute = null)
    {
        if (is_null($attribute)) {
            static::$ownership['*'] = $model;
        }

        static::$ownership[$model] = $attribute;
    }

    /**
     * Determines whether the given model is owned by the given authority.
     *
     * @param  Model  $authority
     * @param  Model  $model
     * @return bool
     */
    public static function isOwnedBy(Model $authority, Model $model)
    {
        $type = get_class($model);

        if (isset(static::$ownership[$type])) {
            $attribute = static::$ownership[$type];
        } elseif (isset(static::$ownership['*'])) {
            $attribute = static::$ownership['*'];
        } else {
            $attribute = strtolower(static::basename($authority)).'_id';
        }

        return static::isOwnedVia($attribute, $authority, $model);
    }

    /**
     * Determines ownership via the given attribute.
     *
     * @param  string|\Closure  $attribute
     * @param  Model  $authority
     * @param  Model  $model
     * @return bool
     */
    protected static function isOwnedVia($attribute, Model $authority, Model $model)
    {
        if ($attribute instanceof Closure) {
            return $attribute($model, $authority);
        }

        return $authority->getKey() == $model->{$attribute};
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

        return new $class($model, Container::getInstance());
    }

    /**
     * Get an instance of the user model.
     *
     * @param  array  $attributes
     * @return Model
     */
    public static function user(array $attributes = [])
    {
        return create_model()->attach($attributes);
    }


    /**
     * Reset all settings to their original state.
     *
     * @return void
     */
    public static function reset()
    {
        static::$models = static::$tables = static::$ownership = [];
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
