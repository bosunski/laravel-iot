<?php

namespace Xeviant\LaravelIot;

use Amp\Loop\DriverFactory;
use Amp\ReactAdapter\ReactAdapter;
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

class MQTTServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MqttRouter::class, function (Application $container) {
            return new MqttRouter($container->get(MQTTClientInterface::class));
        });

        $this->registerEventLoopBindings();

        $this->registerMQTTEventHandlers();
        $this->registerMQTTClient();
        $this->registerMQTTServer();
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
            return new ReactAdapter($app->make('amp.loop'));
        });

        $this->app->singleton(LoopInterface::class, function (Application $app) {
            return new ReactAdapter($app->make('amp.loop'));
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
