<?php

namespace Xeviant\LaravelIot\Tests\Feature;

use Xeviant\LaravelIot\Tests\BaseTestCase;

class ConfigTest extends BaseTestCase
{
    public function test_config_was_loaded()
    {
        $this->assertTrue($this->app['config']->get('mqtt.host') !== null);
    }

    public function test_default_config_was_loaded()
    {
        $this->assertTrue($this->app['config']->get('mqtt.host') === 'test.mosquitto.org');
        $this->assertTrue($this->app['config']->get('mqtt.username') === '');
        $this->assertTrue($this->app['config']->get('mqtt.password') === '');
        $this->assertTrue($this->app['config']->get('mqtt.port') === 1883);
        $this->assertTrue($this->app['config']->get('mqtt.subscription') === 1883);
    }
}
