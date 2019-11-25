<?php

namespace Xeviant\LaravelIot\Tests;

use Orchestra\Testbench\TestCase;
use Xeviant\LaravelIot\MQTTServiceProvider;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            MQTTServiceProvider::class,
        ];
    }
}
