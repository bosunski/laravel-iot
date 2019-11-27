<?php

namespace Xeviant\LaravelIot\Tests\Feature\Commands;

use Xeviant\LaravelIot\Tests\BaseTestCase;

class MqttTopicsCommandTest extends BaseTestCase
{
    public function test_command_exit_with_0()
    {
        $this->artisan('mqtt:topics')
            ->assertExitCode(0);
    }

    public function test_command_list_topics()
    {
        $this->app->make('mqtt.router')->topic("#", "someHandler");
        $this->app->make('mqtt.router')->topic("#", function() {});

        $this->artisan('mqtt:topics')
            ->assertExitCode(0);
    }
}
