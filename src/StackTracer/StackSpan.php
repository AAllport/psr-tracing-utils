<?php

namespace Psr\TracingUtils\StackTracer;

use Psr\Tracing\SpanInterface;
use Stringable;
use Throwable;

class StackSpan implements SpanInterface
{
    /** @var SpanInterface[] $spans */
    public array $spans = [];

    public function __construct(
        string $spanName,
        private StackTracer $tracer
    ) {
        foreach ($this->tracer->getTracers() as $tracer) {
            $this->spans[] = $tracer->createSpan($spanName);
        }
    }

    public function toTraceContextHeaders(): array
    {
        $headers = [];
        foreach ($this->spans as $span) {
            foreach ($span->toTraceContextHeaders() as $header => $value) {
                $headers[$header] = isset($headers[$header])
                    ? $headers[$header] . "," . $value
                    : $value;
            }
        }
    }

    public function setAttribute(string $key, float|Stringable|bool|int|string $value): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->setAttribute($key, $value);
        }

        return $this;
    }

    public function setAttributes(iterable $attributes): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->setAttributes($attributes);
        }

        return $this;
    }

    public function start(): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->start();
        }

        return $this;
    }

    public function activate(): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->activate();
        }

        return $this;
    }

    public function setStatus(int $status, ?string $description): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->setStatus($status, $description);
        }

        return $this;
    }

    public function addException(Throwable $t): SpanInterface
    {
        foreach ($this->spans as $span) {
            $span->addException($t);
        }

        return $this;
    }

    public function finish(): void
    {
        foreach ($this->spans as $span) {
            $span->finish();
        }
    }
}