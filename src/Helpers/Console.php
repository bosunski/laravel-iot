<?php

namespace Xeviant\LaravelIot\Helpers;

use Clue\React\Stdio\Stdio;
use React\EventLoop\LoopInterface;

class Console
{
    /**
     * @var Stdio
     */
    private $stdio;

    public function __construct()
    {
        $this->stdio = new Stdio(resolve(LoopInterface::class));
    }

    public function write($text = ""): void
    {
        $this->stdio->write($text);
    }

    public function writeln($text = ""): void
    {
        $this->stdio->write($text . PHP_EOL);
    }
}
