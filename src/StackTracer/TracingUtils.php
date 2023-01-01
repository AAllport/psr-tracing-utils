<?php

namespace Psr\TracingUtils\StackTracer;

use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;
use Throwable;

class TracingUtils
{
    private TracerInterface $tracer;

    public function __construct(TracerInterface $tracer)
    {
        $this->tracer = $tracer;
    }

    /**
     * @template cbReturn
     * @param SpanInterface|string $span
     * @param callable():cbReturn $callable
     * @return cbReturn
     * @throws Throwable
     */
    public function wrap(SpanInterface|string $span, callable $callable): mixed
    {
        if (is_string($span)) {
            $span = $this->tracer->createSpan($span);
        }

        /** @var SpanInterface $span - Narrow the type now we've handled strings */
        $span->start();

        try {
            return $callable();
        } catch (Throwable $e) {
            $span->addException($e);
            throw $e;
        } finally {
            $span->finish();
        }
    }
}