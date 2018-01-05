<?php

namespace Lxh\Auth\Database\Concerns;

use App\User;
use Lxh\Auth\Database\Role;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Scope\BaseTenantScope;
use Lxh\Auth\Database\Queries\AbilitiesForModel;

trait IsAbility
{
    /**
     * Boot the is ability trait.
     *
     * @return void
     */
    public static function bootIsAbility()
    {
        BaseTenantScope::register(static::class);

        static::creating(function ($ability) {
            Models::scope()->applyToModel($ability);
        });
    }

    /**
     * Create a new ability for a specific model.
     *
     * @param  \Lxh\Database\Eloquent\Model|string  $model
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
     * @param  \Lxh\Database\Eloquent\Model|string  $model
     * @param  string|array  $attributes
     * @return static
     */
    public static function makeForModel($model, $attributes)
    {
        if (is_string($attributes)) {
            $attributes = ['name' => $attributes];
        }

        if ($model === '*') {
            return (new static)->forceFill($attributes + [
                'entity_type' => '*',
            ]);
        }

        if (is_string($model)) {
            $model = new $model;
        }

        return (new static)->forceFill($attributes + [
            'entity_type' => $model->getMorphClass(),
            'entity_id'   => $model->exists ? $model->getKey() : null,
        ]);
    }

    /**
     * The roles relationship.
     *
     * @return \Lxh\Database\Eloquent\Relations\MorphToMany
     */
    public function roles()
    {
        $relation = $this->morphedByMany(
            Models::classname(Role::class),
            'entity',
            Models::table('permissions')
        );

        return Models::scope()->applyToRelation($relation);
    }

    /**
     * The users relationship.
     *
     * @return \Lxh\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        $relation = $this->morphedByMany(
            Models::classname(User::class),
            'entity',
            Models::table('permissions')
        );

        return Models::scope()->applyToRelation($relation);
    }

    /**
     * Get the identifier for this ability.
     *
     * @return string
     */
    final public function getIdentifierAttribute()
    {
        $slug = $this->attributes['name'];

        if ($this->attributes['entity_type']) {
            $slug .= '-'.$this->attributes['entity_type'];
        }

        if ($this->attributes['entity_id']) {
            $slug .= '-'.$this->attributes['entity_id'];
        }

        if ($this->attributes['only_owned']) {
            $slug .= '-owned';
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
     * @param  \Lxh\Database\Eloquent\Builder|\Lxh\Database\Query\Builder  $query
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

        $query->whereIn("{$this->table}.name", $names);
    }

    /**
     * Constrain a query to simple abilities.
     *
     * @param  \Lxh\Database\Eloquent\Builder|\Lxh\Database\Query\Builder  $query
     * @return void
     */
    public function scopeSimpleAbility($query)
    {
        $query->whereNull("{$this->table}.entity_type");
    }

    /**
     * Constrain a query to an ability for a specific model.
     *
     * @param  \Lxh\Database\Eloquent\Builder|\Lxh\Database\Query\Builder  $query
     * @param  \Lxh\Database\Eloquent\Model|string  $model
     * @param  bool  $strict
     * @return void
     */
    public function scopeForModel($query, $model, $strict = false)
    {
        (new AbilitiesForModel)->constrain($query, $model, $strict);
    }
}
