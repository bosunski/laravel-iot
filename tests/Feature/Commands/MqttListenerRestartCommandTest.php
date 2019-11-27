<?php

namespace Xeviant\LaravelIot\Tests\Feature\Commands;

use Xeviant\LaravelIot\Tests\BaseTestCase;

class MqttListenerRestartCommandTest extends BaseTestCase
{
    protected function tearDown(): void
    {
        $this->app['cache']->forget('xeviant:mqtt:restart');
        parent::tearDown();
    }

    public function test_command_exit_with_0()
    {
        $this->artisan('mqtt:restart')
            ->assertExitCode(0);
    }

    public function test_listener_was_restarted()
    {
        $this->app['cache']->forget('xeviant:mqtt:restart');

        $this->artisan('mqtt:restart')
            ->expectsOutput('MQTT Listener Restart Signal Sent')
            ->assertExitCode(0);

        $this->assertNotNull($this->app['cache']->get('xeviant:mqtt:restart'));
    }

}
