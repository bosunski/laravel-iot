<?php

namespace Xeviant\LaravelIot\Tests\Unit;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Xeviant\LaravelIot\Foundation\MqttRouter;
use Xeviant\LaravelIot\Tests\BaseTestCase;

class MqttRouterTest extends BaseTestCase
{
    public function test_topic()
    {
        $router = $this->app->make(MqttRouter::class);
        $this->assertInstanceOf(Route::class, $router->topic('/', 'Handler'));
    }

    public function test_handle()
    {
        $router = $this->app->make(MqttRouter::class);
        $result = "result";

        $router->topic('/', function ($about) use ($result) {
            return $result;
        });

        $this->assertEquals($result, $router->handle("/", "data"));
    }

    public function test_router_can_handle_controller_call()
    {
        $router = $this->app->make(MqttRouter::class);
        $result = "result";

        $router->topic('/', 'Xeviant\LaravelIot\Tests\Unit\TestController@test');

        $this->assertEquals($result, $router->handle("/", "data"));
    }

    public function test_handle_works_when_payload_is_not_passed()
    {
        $router = $this->app->make(MqttRouter::class);
        $result = "result";

        $router->topic('/', function ($about) use ($result) {
            return $result;
        });

        $this->assertEquals($result, $router->handle("/"));
    }

    public function test_handle_throws_error_when_topic_does_not_exist()
    {
        $this->expectException(ResourceNotFoundException::class);

        $router = $this->app->make(MqttRouter::class);

        $router->handle("/not-found-topic", "data");
    }

    public function test_getTopics()
    {
        $router = $this->app->make(MqttRouter::class);

        $router->topic('/', 'AbleTopicHandler');
        $this->assertIsArray($router->getTopics());
    }

    public function test_getTopics_returns_non_empty_after_topic_is_added()
    {
        $router = $this->app->make(MqttRouter::class);
        $router->topic('/', 'AbleTopicHandler');
        $this->assertNotEmpty($router->getTopics());
    }

    public function test_getTopics_returns_empty_when_no_topic_is_added()
    {
        $router = $this->app->make(MqttRouter::class);
        $this->assertEmpty($router->getTopics());
    }
}

class TestController {
    public function test()
    {
        return "result";
    }
}
