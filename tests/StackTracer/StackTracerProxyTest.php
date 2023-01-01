<?php

namespace Psr\TracingUtilsTests\StackTracer;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Tracing\SpanInterface;
use Psr\Tracing\TracerInterface;
use Psr\TracingUtils\StackTracer\StackTracer;
use Psr\TracingUtilsTests\Fixtures\MockTracer;
use Psr\TracingUtilsTests\UsesPeak;

class StackTracerProxyTest extends TestCase
{
    use UsesPeak;

    public function testMockTracer()
    {
        [$stackSpan, $span1, $span2] = $this->generateMockTracer();

        $this->assertCount(2, $this->peak($stackSpan, "spans"));
    }

    /** @return array{0:TracerInterface, 1:SpanInterface&MockObject,2:SpanInterface&MockObject} */
    public function generateMockTracer(): array
    {
        $span1 = $this->createMock(SpanInterface::class);
        $span2 = $this->createMock(SpanInterface::class);

        $tracer1 = new MockTracer((fn($spanName) => $span1));
        $tracer2 = new MockTracer((fn($spanName) => $span2));

        $stackTracer = (new StackTracer())
            ->pushTracer($tracer1)
            ->pushTracer($tracer2);

        $stackSpan = $stackTracer->createSpan("fooSpan");

        return [$stackSpan, $span1, $span2];
    }

    public function proxiesCallProvider(): array
    {
        return [
            'setAttribute' => ['setAttribute', ['foo', 'bar']],
            'setAttributes' => ['setAttributes', [['foo' => 'bar']]],
            'start' => ['start', []],
            'activate' => ['activate', []],
            'setStatus_ok' => ['setStatus', [SpanInterface::STATUS_OK, 'OK']],
            'setStatus_error' => ['setStatus', [SpanInterface::STATUS_ERROR, 'OK']],
            'addException' => ['addException', [new Exception()]],
            'finish' => ['finish', []],
        ];
    }

    /** @dataProvider proxiesCallProvider */
    public function testProxiesCall($name, $args)
    {
        [$stackSpan, $span1, $span2] = $this->generateMockTracer();

        $span1
            ->expects($this->once())
            ->method($name)
            ->with(...$args);
        $span2
            ->expects($this->once())
            ->method($name)
            ->with(...$args);

        $stackSpan->{$name}(...$args);
    }
}