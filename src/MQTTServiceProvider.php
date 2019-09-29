<?php

namespace Bosunski\LaravelIot;

use Amp\Loop\DriverFactory;
use Amp\ReactAdapter\ReactAdapter;
use App\Foundation\Mqtt;
use App\Foundation\MQTTClient;
use App\Foundation\MqttHandler;
use App\Foundation\MQTTServer;
use App\Mqtt\Contracts\MQTTClientInterface;
use App\Mqtt\Contracts\MQTTHandlerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use React\Dns\Resolver\Factory as DNSResolverFactory;
use React\EventLoop\LoopInterface;
use React\MySQL\Factory;
use React\Socket\DnsConnector;
use React\Socket\TcpConnector;
use Workerman\Mqtt\Client;

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
        $this->app->singleton(Mqtt::class, function ($container) {
            return new Mqtt();
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

        $this->app->singleton('amp.loop', function ($app) {
            return (new DriverFactory())->create();
        });
    }
}
