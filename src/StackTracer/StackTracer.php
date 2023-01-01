<?php

namespace Psr\TracingUtils\StackTracer;

use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;

class StackTracer implements TracerInterface
{
    private array $stack = [];

    public function pushTracer(TracerInterface $tracer): StackTracer
    {
        $this->stack[] = $tracer;
        return $this;
    }

    public function getTracers(): array
    {
        return $this->stack;
    }

    public function createSpan(string $spanName): SpanInterface
    {
        return new StackSpan($spanName, $this);
    }

    public function getCurrentTraceId(): string
    {
        return $this->stack[0]->getCurrentTraceId() ?? "STACK-SPAN";
    }
}