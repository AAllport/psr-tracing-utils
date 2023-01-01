<?php

namespace Psr\TracingUtilsTests\Concerns;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;
use Psr\TracingUtils\StackTracer\StackTracer;
use Psr\TracingUtilsTests\Fixtures\MockTracer;

trait UsesMockTracing
{
    /** @return array{0:TracerInterface, 1:SpanInterface&MockObject,2:SpanInterface&MockObject} */
    public function generateMockStackTracer(): array
    {
        [$tracer1, $span1] = $this->generateMockTracer();
        [$tracer2, $span2] = $this->generateMockTracer();

        $stackTracer = (new StackTracer())
            ->pushTracer($tracer1)
            ->pushTracer($tracer2);

        $stackSpan = $stackTracer->createSpan("fooSpan");

        return [$stackSpan, $span1, $span2];
    }

    /** @return array{0:TracerInterface, 1:SpanInterface} */
    public function generateMockTracer(): array
    {
        $span = $this->createMock(SpanInterface::class);
        $tracer = new MockTracer((fn($spanName) => $span));
        return [$tracer, $span];
    }
}