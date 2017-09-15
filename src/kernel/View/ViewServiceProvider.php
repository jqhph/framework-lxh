<?php

namespace Lxh\View;

use Lxh\Support\ServiceProvider;
use Lxh\View\Engines\PhpEngine;
use Lxh\View\Engines\FileEngine;
use Lxh\View\Engines\CompilerEngine;
use Lxh\View\Engines\EngineResolver;
use Lxh\View\Compilers\BladeCompiler;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFactory();

        $this->registerViewFinder();

        $this->registerEngineResolver();
    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->container->singleton('view.factory', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app->make('view.engine.resolver');

            $finder = $app->make('view.finder');

            $env = new Factory($resolver, $finder, $app->make('events'));

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);

            $env->share('app', $app);

            return $env;
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->container->bind('view.finder', function ($app) {
            return new FileViewFinder($app->make('file.manager'), config('view.paths', 'resource/views'));
        });
    }

    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        $this->container->singleton('view.engine.resolver', function () {
            $resolver = new EngineResolver;

            // Next, we will register the various view engines with the resolver so that the
            // environment will resolve the engines needed for various views based on the
            // extension of view file. We call a method for each of the view's engines.
            $this->registerFileEngine($resolver);
            $this->registerPhpEngine($resolver);
            $this->registerBladeEngine($resolver);

            return $resolver;
        });
    }

    /**
     * Register the file engine implementation.
     *
     * @param  \Lxh\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerFileEngine($resolver)
    {
        $resolver->register('file', function () {
            return new FileEngine;
        });
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \Lxh\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerPhpEngine($resolver)
    {
        $resolver->register('php', function () {
            return new PhpEngine;
        });
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Lxh\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerBladeEngine($resolver)
    {
        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $this->container->singleton('blade.compiler', function () {
            return new BladeCompiler(
                $this->container->make('file.manager'), config('view.compiled', 'resource/blade-cache')
            );
        });

        $resolver->register('blade', function () {
            return new CompilerEngine($this->container->make('blade.compiler'));
        });
    }
}
