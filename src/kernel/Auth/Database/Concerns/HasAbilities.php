<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Container\Container;

use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Ability;
use Lxh\Auth\Conductors\GivesAbilities;
use Lxh\Auth\Conductors\ForbidsAbilities;
use Lxh\Auth\Conductors\RemovesAbilities;
use Lxh\Auth\Conductors\UnforbidsAbilities;

trait HasAbilities
{
    /**
     * The abilities relationship.
     *
     * @return \Lxh\Database\Eloquent\Relations\MorphToMany
     */
    public function abilities()
    {
        $relation = $this->morphToMany(
            Models::classname(Ability::class),
            'entity',
            Models::table('permissions')
        );

        return Models::scope()->applyToRelation($relation);
    }

    /**
     * Get all of the model's allowed abilities.
     *
     * @return \Lxh\Database\Eloquent\Collection
     */
    public function getAbilities()
    {
        return $this->getClipboardInstance()->getAbilities($this);
    }

    /**
     * Get all of the model's allowed abilities.
     *
     * @return \Lxh\Database\Eloquent\Collection
     */
    public function getForbiddenAbilities()
    {
        return $this->getClipboardInstance()->getAbilities($this, false);
    }

    /**
     * Give an ability to the model.
     *
     * @param  mixed  $ability
     * @param  mixed|null  $model
     * @return \Lxh\Auth\Conductors\GivesAbilities|$this
     */
    public function allow($ability = null, $model = null)
    {
        if (is_null($ability)) {
            return new GivesAbilities($this);
        }

        (new GivesAbilities($this))->to($ability, $model);

        return $this;
    }

    /**
     * Remove an ability from the model.
     *
     * @param  mixed  $ability
     * @param  mixed|null  $model
     * @return \Lxh\Auth\Conductors\RemovesAbilities|$this
     */
    public function disallow($ability = null, $model = null)
    {
        if (is_null($ability)) {
            return new RemovesAbilities($this);
        }

        (new RemovesAbilities($this))->to($ability, $model);

        return $this;
    }

    /**
     * Forbid an ability to the model.
     *
     * @param  mixed  $ability
     * @param  mixed|null  $model
     * @return \Lxh\Auth\Conductors\ForbidsAbilities|$this
     */
    public function forbid($ability = null, $model = null)
    {
        if (is_null($ability)) {
            return new ForbidsAbilities($this);
        }

        (new ForbidsAbilities($this))->to($ability, $model);

        return $this;
    }

    /**
     * Remove ability forbiddal from the model.
     *
     * @param  mixed  $ability
     * @param  mixed|null  $model
     * @return \Lxh\Auth\Conductors\UnforbidsAbilities|$this
     */
    public function unforbid($ability = null, $model = null)
    {
        if (is_null($ability)) {
            return new UnforbidsAbilities($this);
        }

        (new UnforbidsAbilities($this))->to($ability, $model);

        return $this;
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
