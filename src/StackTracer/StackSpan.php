<?php

namespace Psr\TracingUtils\StackTracer;

use Psr\Tracing\SpanInterface;
use Stringable;
use Throwable;

class StackSpan implements SpanInterface
{
    /** @var SpanInterface[] $spans */
    public array $spans = [];

    public ?self $parent = null;
    /** @var array<self> $children */
    public array $children = [];

    public function __construct(
        string $spanName,
        private StackTracer $tracer
    ) {
        if ($this->tracer->getRootSpan() === null) {
            $this->tracer->setRootSpan($this);
        }

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
        return $headers;
    }

    public function setAttribute(string $key, null|float|Stringable|bool|int|string $value): SpanInterface
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

        $this->tracer->setCurrentSpan($this);

        return $this;
    }

    public function setStatus(int $status, ?string $description = null): SpanInterface
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

        $this->tracer->setCurrentSpan($this->parent);
    }

    public function getAttribute(string $key): null|string|int|float|bool
    {
        foreach ($this->spans as $span) {
            $value = $span->getAttribute($key);
            if ($value !== null) {
                return $value;
            }
        }
        return null;
    }

    public function getAttributes(): iterable
    {
        $attributes = [];
        foreach (array_reverse($this->spans) as $span) {
            foreach ($span->getAttributes() as $key => $value) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    public function createChild(string $spanName): SpanInterface
    {
        $child = new self($spanName, $this->tracer);
        $child->spans = [];

        foreach ($this->spans as $span) {
            $child->spans[] = $span->createChild($spanName);
        }

        return $child;
    }

    public function getParent(): ?SpanInterface
    {
        return $this->getParent();
    }

    public function getChildren(): iterable
    {
        return $this->children;
    }
}
