<?php

namespace Xeviant\LaravelIot;

use Amp\Loop\DriverFactory;
use Amp\ReactAdapter\ReactAdapter;
use Xeviant\LaravelIot\Console\Commands\MqttListenerStart;
use Xeviant\LaravelIot\Console\Commands\RestartMQTTServer;
use Xeviant\LaravelIot\Foundation\MqttRouter;
use Xeviant\LaravelIot\Foundation\MQTTClient;
use Xeviant\LaravelIot\Foundation\MqttHandler;
use Xeviant\LaravelIot\Foundation\MQTTListener;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use React\Dns\Resolver\Factory as DNSResolverFactory;
use React\EventLoop\LoopInterface;
use React\Socket\DnsConnector;
use React\Socket\TcpConnector;

class LaravelMqttServiceProviderSample extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-iot');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-iot');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-iot.php'),
            ], 'laravel-mqtt-config');

            $this->publishes([
                __DIR__.'/../routes/mqtt.php' => base_path('routes/topics.php'),
            ], 'laravel-mqtt-topics');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-iot'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-iot'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-iot'),
            ], 'lang');*/

            // Registering package commands.

            $this->app->singleton('command.mqtt.server.start', function ($app) {
                return new MqttListenerStart;
            });

            $this->app->singleton('command.mqtt.server.restart', function ($app) {
                return new RestartMQTTServer;
            });

             $this->commands([
                 MqttListenerStart::class,
                 RestartMQTTServer::class,
             ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-iot');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-iot', function () {
            return new LaravelIot;
        });

        $this->app->singleton(MqttRouter::class, function ($container) {
            return new MqttRouter();
        });

        $this->registerEventLoopBindings();

        $this->registerMysql();
        $this->registerMQTTEventHandlers();
        $this->registerMQTTClient();
        $this->registerMQTTServer();
    }

    protected function registerMysql()
    {
        $this->app->singleton('react.db.connection', function (Application $app) {
            $config = (object) config('database.connections.mysql');

            $factory = new Factory($app->make(LoopInterface::class));
            $uri = "$config->username:$config->password@$config->host/$config->database";

            return $factory->createLazyConnection($uri);
        });
    }

    protected function registerMQTTClient()
    {
        $this->app->singleton(MQTTClientInterface::class, function (Application $application) {
            $loop = $application->make(LoopInterface::class);
            $dnsResolverFactory = new DNSResolverFactory();
            $connector = new DnsConnector(new TcpConnector($loop), $dnsResolverFactory->createCached('8.8.8.8', $loop));
            return new MQTTClient($connector, $loop);
        });
    }

    protected function registerMQTTServer()
    {
        $this->app->singleton('xeviant.mqtt.server', MQTTListener::class);
    }

    protected function registerMQTTEventHandlers()
    {
        $this->app->singleton(MQTTHandlerInterface::class, MqttHandler::class);
    }

    protected function registerEventLoopBindings()
    {
        $this->app->singleton(LoopInterface::class, function (Application $app) {
            return new ReactAdapter($app->make('amp.loop'));
        });

        $this->app->singleton('amp.loop', function ($app) {
            return (new DriverFactory())->create();
        });
    }
}
