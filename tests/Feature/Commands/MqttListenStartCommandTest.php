<?php

namespace Xeviant\LaravelIot\Tests\Feature\Commands;

use Exception;
use Xeviant\LaravelIot\Foundation\MQTTListener;
use Xeviant\LaravelIot\Tests\Cases\LoopBasedTestCase;

class MqttListenStartCommandTest extends LoopBasedTestCase
{
    /**
     * This test will fail if
     */
    public function test_listener_returns_promise()
    {
        /**
         * @var $listener MQTTListener
         */
        $listener = $this->app->make(MQTTListener::class);
        $listener->listen()->then(function ($isStarted) {
            $this->assertTrue($isStarted);
        }, function (Exception $exception) {
            var_dump($exception->getMessage());
            $this->markTestIncomplete("Connection Failed with message: {$exception->getMessage()}");
        });

        $this->startLoop();
    }
}
