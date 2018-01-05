<?php

namespace Lxh\Auth\Conductors\Lazy;

class ConductsAbilities
{
    /**
     * The conductor handling the permission.
     *
     * @var \Lxh\Auth\Conductors\Concerns\ConductsAbilities
     */
    protected $conductor;

    /**
     * The abilities to which ownership is restricted.
     *
     * @var string|string[]
     */
    protected $abilities;

    /**
     * Determines whether the given abilities should be granted on all models.
     *
     * @var bool
     */
    protected $everything = false;

    /**
     * The extra attributes for the abilities.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     *
     * @param \Lxh\Auth\Conductors\Concerns\ConductsAbilities  $conductor
     * @param mixed  $model
     * @param array  $attributes
     */
    public function __construct($conductor, $abilities)
    {
        $this->conductor = $conductor;
        $this->abilities = $abilities;
    }

    /**
     * Sets that the abilities should be applied towards everything.
     *
     * @param  array  $attributes
     * @return void
     */
    public function everything(array $attributes = [])
    {
        $this->everything = true;

        $this->attributes = $attributes;
    }

    /**
     * Destructor.
     *
     */
    public function __destruct()
    {
        $this->conductor->then(
            $this->abilities,
            $this->everything ? '*' : null,
            $this->attributes
        );
    }
}
