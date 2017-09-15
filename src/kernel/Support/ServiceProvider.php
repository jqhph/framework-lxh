<?php

namespace Lxh\Support;

use Lxh\Console\Application as Artisan;
use Lxh\Contracts\Container\Container;

abstract class ServiceProvider
{
    /**
     * The application instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new service provider instance.
     *
     * @param  Container $app
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register();

}
