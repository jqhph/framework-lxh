<?php

namespace Lxh\Admin\Providers;

use Lxh\Admin\Facades\Admin;
use Lxh\Foundation\AliasLoader;
use Lxh\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        'Lxh\Admin\Commands\MakeCommand',
        'Lxh\Admin\Commands\MenuCommand',
        'Lxh\Admin\Commands\InstallCommand',
        'Lxh\Admin\Commands\UninstallCommand',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth'       => \Lxh\Admin\Middleware\Authenticate::class,
        'admin.pjax'       => \Lxh\Admin\Middleware\PjaxMiddleware::class,
        'admin.log'        => \Lxh\Admin\Middleware\OperationLog::class,
        'admin.permission' => \Lxh\Admin\Middleware\PermissionMiddleware::class,
        'admin.bootstrap'  => \Lxh\Admin\Middleware\BootstrapMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.pjax',
            'admin.log',
            'admin.bootstrap',
        ],
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
//        $this->loadViewsFrom(__DIR__.'/../../views', 'admin');
//        $this->loadTranslationsFrom(__DIR__.'/../../lang/', 'admin');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function setupAuth()
    {
//        config([
//            'auth.guards.admin.driver'    => 'session',
//            'auth.guards.admin.provider'  => 'admin',
//            'auth.providers.admin.driver' => 'eloquent',
//            'auth.providers.admin.model'  => config('admin.database.users_model'),
//        ]);
    }

}
