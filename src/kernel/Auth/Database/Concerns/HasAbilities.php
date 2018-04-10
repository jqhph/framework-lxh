<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Container\Container;

use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Models;
use Lxh\Auth\Database\Ability;
use Lxh\Auth\Conductors\ForbidsAbilities;
use Lxh\Support\Collection;

trait HasAbilities
{
    /**
     * Get all of the model's allowed abilities.
     *
     * @return Collection
     */
    public function getAbilities()
    {
        return $this->getClipboardInstance()->getAbilities();
    }

    /**
     * Get all of the model's allowed abilities.
     *
     * @return Collection
     */
    public function getForbiddenAbilities()
    {
        return $this->getClipboardInstance()->getAbilities();
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
