<?php

namespace Xeviant\LaravelIot;

use Amp\Loop\DriverFactory;
use React\EventLoop\Factory;
use Xeviant\LaravelIot\Console\Commands\MqttServerStart;
use Xeviant\LaravelIot\Console\Commands\RestartMQTTServer;
use Xeviant\LaravelIot\Foundation\MqttPublisher;
use Xeviant\LaravelIot\Foundation\MqttRouter;
use Xeviant\LaravelIot\Foundation\MQTTClient;
use Xeviant\LaravelIot\Foundation\MqttHandler;
use Xeviant\LaravelIot\Foundation\MQTTServer;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use React\Dns\Resolver\Factory as DNSResolverFactory;
use React\EventLoop\LoopInterface;
use React\Socket\DnsConnector;
use React\Socket\TcpConnector;

class LaravelMQTTServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . "/../config/config.php" => config_path("mqtt.php")
            ], 'laravel-mqtt-config');

            $this->publishes([
                __DIR__ . "/../routes/mqtt.php" => config_path("topics.php")
            ], 'laravel-mqtt-topics');

            $this->bootCommands();
        }
    }

    public function bootCommands()
    {
        $this->app->singleton('command.mqtt.server.start', function ($app) {
            return new MqttServerStart;
        });

        $this->app->singleton('command.mqtt.server.restart', function ($app) {
            return new RestartMQTTServer;
        });

        $this->commands([
            MqttServerStart::class,
            RestartMQTTServer::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerEventLoopBindings();
        $this->registerMQTTEventHandlers();
        $this->registerMQTTClient();
        $this->registerMQTTServer();
        $this->loadConfiguration();
        $this->registerMQTTPublisher();
    }

    public function registerRouter()
    {
        $this->app->singleton(MqttRouter::class, function (Application $container) {
            return new MqttRouter($container->get(MQTTClientInterface::class));
        });
    }

    public function loadConfiguration()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/config.php", "mqtt");
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
        $this->app->singleton('xeviant.mqtt.server', MQTTServer::class);
    }

    protected function registerMQTTEventHandlers()
    {
        $this->app->singleton(MQTTHandlerInterface::class, MqttHandler::class);
    }

    protected function registerEventLoopBindings()
    {
        $this->app->singleton(LoopInterface::class, function (Application $app) {
            return Factory::create();
        });

        $this->app->singleton('amp.loop', function ($app) {
            return (new DriverFactory())->create();
        });
    }

    protected function registerMQTTPublisher()
    {
        $this->app->singleton('mqtt.publisher', function(Application $app) {
            return $app->make(MqttPublisher::class);
        });
    }
}
