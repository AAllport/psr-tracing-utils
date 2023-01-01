<?php

namespace Psr\TracingUtilsTests\Fixtures;

use Psr\Tracing\SpanInterface;
use Stringable;
use Throwable;

class NoopSpan implements SpanInterface
{

    /** @noinspection PhpPropertyOnlyWrittenInspection */
    public function __construct(
        private string $spanName
    ) {
    }

    public function toTraceContextHeaders(): array
    {
        return [];
    }

    public function setAttribute(string $key, float|Stringable|bool|int|string $value): SpanInterface
    {
        return $this;
    }

    public function setAttributes(iterable $attributes): SpanInterface
    {
        return $this;
    }

    public function start(): SpanInterface
    {
        return $this;
    }


    public function activate(): SpanInterface
    {
        return $this;
    }

    public function setStatus(int $status, ?string $description): SpanInterface
    {
        return $this;
    }

    public function addException(Throwable $t): SpanInterface
    {
        return $this;
    }

    public function finish(Throwable $error = null): void
    {
    }
}