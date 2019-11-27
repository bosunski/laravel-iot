<?php

namespace Xeviant\LaravelIot\Tests\Feature\Commands;

use Exception;
use React\Promise\PromiseInterface;
use Xeviant\LaravelIot\Foundation\MQTTListener;
use Xeviant\LaravelIot\Tests\Cases\LoopBasedTestCase;

class MqttListenerTest extends LoopBasedTestCase
{
    /**
     * This test will fail if
     */
    public function test_listener_connection_was_successful()
    {
        /**
         * @var $listener MQTTListener
         */
        $listener = $this->app->make(MQTTListener::class);
        $connection = $listener->listen();
        $this->assertInstanceOf(PromiseInterface::class, $connection);


        $connection->then(function ($isStarted) {
            $this->assertTrue($isStarted);
        }, function (Exception $exception) {
            $this->fail("Connection Failed with message: {$exception->getMessage()}");
        });

        $this->startLoop();
    }
}
