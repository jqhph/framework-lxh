<?php

namespace Lxh\Mail;

use Swift_Mailer;
use Lxh\Support\Arr;
use Lxh\Support\Str;
use Lxh\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSwiftMailer();

        $this->registerLxhMailer();

        $this->registerMarkdownRenderer();
    }

    /**
     * Register the Lxh mailer instance.
     *
     * @return void
     */
    protected function registerLxhMailer()
    {
        $this->container->singleton('mailer', function ($container) {
            $config = $container->make('config')->get('mail');

            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This allows us to resolve mailer classes via containers
            // for maximum testability on said classes instead of passing Closures.
            $mailer = new Mailer(
                $container['viewFactory'], $container['swift.mailer'], $container['events']
            );

            if ($container->bound('queue')) {
                $mailer->setQueue($container['queue']);
            }

            // Next we will set all of the global addresses on this mailer, which allows
            // for easy unification of all "from" addresses as well as easy debugging
            // of sent messages since they get be sent into a single email address.
            foreach (['from', 'reply_to', 'to'] as & $type) {
                $this->setGlobalAddress($mailer, $config, $type);
            }

            return $mailer;
        });
    }

    /**
     * Set a global address on the mailer by type.
     *
     * @param  \Lxh\Mail\Mailer  $mailer
     * @param  array  $config
     * @param  string  $type
     * @return void
     */
    protected function setGlobalAddress($mailer, array $config, $type)
    {
        $address = Arr::get($config, $type);

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always'.Str::studly($type)}($address['address'], $address['name']);
        }
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerSwiftTransport();

        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.
        $this->container->singleton('swift.mailer', function ($container) {
            return new Swift_Mailer($container['swift.transport']->driver());
        });
    }

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->container->singleton('swift.transport', function ($container) {
            return new TransportManager($container);
        });
    }

    /**
     * Register the Markdown renderer instance.
     *
     * @return void
     */
    protected function registerMarkdownRenderer()
    {
//        if ($this->container->runningInConsole()) {
//            $this->publishes([
//                __DIR__.'/resources/views' => $this->container->resourcePath('views/vendor/mail'),
//            ], 'laravel-mail');
//        }

        $this->container->singleton(Markdown::class, function ($container) {
            $config = $container->make('config');

            return new Markdown($container->make('view'), [
                'theme' => $config->get('mail.markdown.theme', 'default'),
                'paths' => $config->get('mail.markdown.paths', []),
            ]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'mailer', 'swift.mailer', 'swift.transport', Markdown::class,
        ];
    }
}
