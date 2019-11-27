<?php

namespace Xeviant\LaravelIot\Tests\Cases;

use React\EventLoop\LoopInterface;
use Xeviant\LaravelIot\Tests\BaseTestCase;

class LoopBasedTestCase extends BaseTestCase
{
    /**
     * @var LoopInterface
     */
    private $loop;

    const MAXIMUM_EXECUTION_TIMEOUT = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loop = $this->app->make(LoopInterface::class);

        $this->loop->addPeriodicTimer(self::MAXIMUM_EXECUTION_TIMEOUT, function () {
            $this->loop->stop();
        });
    }

    public function tearDown(): void
    {
        $this->loop->stop();
    }

    public function startLoop(): void
    {
        $this->loop->run();
    }

    public function stopLoop(): void
    {
        $this->loop->stop();
    }
}
