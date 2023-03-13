<?php

namespace Psr\TracingUtils\StackTracer;

use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;

class StackTracer implements TracerInterface
{
    private array $stack = [];

    private ?StackSpan $root = null;
    private ?StackSpan $current = null;

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

    public function getRootSpan(): ?SpanInterface
    {
        return $this->root;
    }

    public function getCurrentSpan(): ?SpanInterface
    {
        return $this->current;
    }

    public function setRootSpan(StackSpan $span): void
    {
        $this->root = $span;
    }
    public function setCurrentSpan(StackSpan $span): void
    {
        $this->current = $span;
    }
}
