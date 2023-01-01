<?php

namespace Psr\TracingUtilsTests;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\TracingUtils\StackTracer\TracingUtils;
use Psr\TracingUtilsTests\Concerns\UsesMockTracing;


class TracingUtilsTest extends TestCase
{
    use UsesMockTracing;

    public function testWrap(): void
    {
        [$tracer, $span] = $this->generateMockTracer();
        $tracingUtils = new TracingUtils($tracer);

        $span->expects($this->once())
            ->method('start');
        $span->expects($this->once())
            ->method('finish');

        $tracingUtils->wrap("foo", function () {
            return "bar";
        });
    }

    public function testWrapWithException(): void
    {
        [$tracer, $span] = $this->generateMockTracer();
        $tracingUtils = new TracingUtils($tracer);

        $exception = new Exception("foo");

        $span->expects($this->once())
            ->method('start');

        $span->expects($this->once())
            ->method('addException')
            ->with($exception);

        $span->expects($this->once())
            ->method('finish');

        $this->expectException(Exception::class);

        $tracingUtils->wrap("foo", function () use ($exception) {
            throw $exception;
        });
    }

}