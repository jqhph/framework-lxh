<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Helpers;
use Lxh\Container\Container;
use Lxh\MVC\Model;
use Lxh\Auth\Database\Role;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Queries\AbilitiesForModel;
use Lxh\Support\Arr;
use Lxh\Support\Collection;

trait IsAbility
{
    /**
     * Boot the is ability trait.
     *
     * @return void
     */
    public static function bootIsAbility()
    {
    }

    /**
     * Create a new ability for a specific model.
     *
     * @param  Model|string  $model
     * @param  string|array  $attributes
     * @return static
     */
    public static function createForModel($model, $attributes)
    {
        $model = static::makeForModel($model, $attributes);

        $model->save();

        return $model;
    }

    /**
     * Make a new ability for a specific model.
     *
     * @param  Model|string  $model
     * @param  string|array  $attributes
     * @return static
     */
    public static function makeForModel($model, $attributes)
    {
        if (is_string($attributes)) {
            $attributes = ['name' => $attributes];
        }

        return Models::ability()->attach((array) $attributes);
    }

    /**
     * The roles relationship.
     *
     * @return array
     */
    public function roles()
    {
        $relation = $this->morphedByMany(
            Models::classname(Role::class),
            'entity',
            Models::table('permissions')
        );
    }

    /**
     * The users relationship.
     *
     * @return array
     */
    public function users()
    {
        $relation = $this->morphedByMany(
            Models::classname(User::class),
            'entity',
            Models::table('permissions')
        );
    }

    /**
     * Get the identifier for this ability.
     *
     * @return string
     */
    final public function getIdentifierAttribute()
    {
        $slug = $this->items['name'];

        if ($this->items['entity_type']) {
            $slug .= '-'.$this->items['entity_type'];
        }

        if ($this->items['entity_id']) {
            $slug .= '-'.$this->items['entity_id'];
        }

        return strtolower($slug);
    }

    /**
     * Get the ability's "slug" attribute.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->getIdentifierAttribute();
    }

    /**
     * Constrain a query to having the given name.
     *
     * @param   $query
     * @return string|array  $name
     * @return bool  $strict
     * @return void
     */
    public function scopeByName($query, $name, $strict = false)
    {
        $names = (array) $name;

        if ( ! $strict) {
            $names[] = '*';
        }

        $query->where("{$this->table}.name", 'IN', $names);
    }

    /**
     * Constrain a query to simple abilities.
     *
     * @param    $query
     * @return void
     */
    public function scopeSimpleAbility($query)
    {
        $query->whereNull("{$this->table}.entity_type");
    }

    /**
     * Constrain a query to an ability for a specific model.
     *
     * @param  $query
     * @param  Model|string  $model
     * @param  bool  $strict
     * @return void
     */
    public function scopeForModel($query, $model, $strict = false)
    {
        (new AbilitiesForModel)->constrain($query, $model, $strict);
    }
}
