<?php

namespace Psr\TracingUtilsTests\StackTracer;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Tracing\SpanInterface;
use Psr\TracingUtilsTests\Concerns\UsesMockTracing;
use Psr\TracingUtilsTests\Concerns\UsesPeak;

class StackTracerProxyTest extends TestCase
{
    use UsesMockTracing;
    use UsesPeak;


    public function testMockTracer()
    {
        [$stackSpan, $span1, $span2] = $this->generateMockStackTracer();

        $this->assertCount(2, $this->peak($stackSpan, "spans"));
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
        [$stackSpan, $span1, $span2] = $this->generateMockStackTracer();

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