<?php

namespace Xeviant\LaravelIot\Tests\Unit;

use React\Promise\PromiseInterface;
use Xeviant\LaravelIot\Foundation\MqttPublisher;
use Xeviant\LaravelIot\Tests\BaseTestCase;

class MqttPublisherTest extends BaseTestCase
{
    public function test_publish()
    {
        $publisher = $this->app->make(MqttPublisher::class);

        $this->assertInstanceOf(
            PromiseInterface::class,
            $publisher->publish("/", "")
        );
    }
}
