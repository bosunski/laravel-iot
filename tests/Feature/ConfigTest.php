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
        $this->assertTrue($this->app['config']->get('mqtt.host') === '127.0.0.1');
        $this->assertTrue($this->app['config']->get('mqtt.username') === 'guest');
        $this->assertTrue($this->app['config']->get('mqtt.password') === 'guest');
        $this->assertTrue($this->app['config']->get('mqtt.port') === 1883);
    }
}
