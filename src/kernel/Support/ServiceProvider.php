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

    /**
     * Register the package's custom Artisan commands.
     *
     * @param  array|mixed  $commands
     * @return void
     */
    public function commands($commands)
    {
        $this->container['app']->addCommands(
            is_array($commands) ? $commands : func_get_args()
        );
    }


}
