<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Clipboard;
use Lxh\Container\Container;
use Lxh\MVC\Model;

trait Authorizable
{
    /**
     * Determine if the authority has a given ability.
     *
     * @param  string  $ability
     * @param  Model|null  $model
     * @return bool
     */
    public function can($ability, $model = null)
    {
        return $this->getClipboardInstance()->check($this, $ability, $model);
    }

    /**
     * Determine if the authority does not have a given ability.
     *
     * @param  string  $ability
     * @param  Model|null  $model
     * @return bool
     */
    public function cant($ability, $model = null)
    {
        return ! $this->can($ability, $model);
    }

    /**
     * Determine if the authority does not have a given ability.
     *
     * @param  string  $ability
     * @param  Model|null  $model
     * @return bool
     */
    public function cannot($ability, $model = null)
    {
        return $this->cant($ability, $model);
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
