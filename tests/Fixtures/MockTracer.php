<?php

namespace Psr\TracingUtilsTests\Fixtures;

use Closure;
use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;

class MockTracer implements TracerInterface
{

    /** @var Closure(string):SpanInterface $cbCreateSpan */
    private Closure $cbCreateSpan;

    public function __construct(callable $cbCreateSpan = null)
    {
        $this->cbCreateSpan = $cbCreateSpan ?? fn($spanName) => new NoopSpan($spanName);
    }


    public function createSpan(string $spanName): SpanInterface
    {
        $currentSpan = ($this->cbCreateSpan)($spanName);
        return $currentSpan;
    }

    public function getCurrentTraceId(): string
    {
        // TODO: Implement getCurrentTraceId() method.
    }
}