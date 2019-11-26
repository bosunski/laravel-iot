<?php

namespace Xeviant\LaravelIot\Tests;

use Orchestra\Testbench\TestCase;
use Xeviant\LaravelIot\LaravelMQTTServiceProvider;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMQTTServiceProvider::class,
        ];
    }
}
