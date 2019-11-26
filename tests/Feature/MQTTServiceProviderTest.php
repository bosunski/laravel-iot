<?php

namespace Xeviant\LaravelIot\Tests\Feature;

use Amp\Loop\Driver;
use React\EventLoop\LoopInterface;
use Xeviant\LaravelIot\Console\Commands\MqttServerStart;
use Xeviant\LaravelIot\Console\Commands\RestartMQTTServer;
use Xeviant\LaravelIot\Foundation\MqttPublisher;
use Xeviant\LaravelIot\Foundation\MqttRouter;
use Xeviant\LaravelIot\Foundation\MQTTServer;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use Xeviant\LaravelIot\Tests\BaseTestCase;

class MQTTServiceProviderTest extends BaseTestCase
{
    public function test_router_was_registered()
    {
        $this->assertInstanceOf(MqttRouter::class, $this->app->make(MqttRouter::class));
    }

    public function test_event_loop_bindings_were_registered()
    {
        $this->assertInstanceOf(LoopInterface::class, $this->app->make(LoopInterface::class));
        $this->assertInstanceOf(Driver::class, $this->app->make('amp.loop'));
    }

    public function test_mqtt_event_handlers_were_registered()
    {
        $this->assertInstanceOf(MQTTHandlerInterface::class, $this->app->make(MQTTHandlerInterface::class));
    }

    public function test_mqtt_client_was_registered()
    {
        $this->assertInstanceOf(MQTTClientInterface::class, $this->app->make(MQTTClientInterface::class));
    }

    public function test_mqtt_server_was_registered()
    {
        $this->assertInstanceOf(MQTTServer::class, $this->app->make('xeviant.mqtt.server'));
    }

    public function test_mqtt_publisher_was_registered()
    {
        $this->assertInstanceOf(MqttPublisher::class, $this->app->make('mqtt.publisher'));
    }

    public function test_mqtt_commands_were_registered_at_boot()
    {
        if ($this->app->isBooted()) {
            $this->assertInstanceOf(MqttServerStart::class, $this->app->make('command.mqtt.server.start'));
            $this->assertInstanceOf(RestartMQTTServer::class, $this->app->make('command.mqtt.server.restart'));
        }
    }
}
