<?php

namespace Psr\TracingUtilsTests;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @use TestCase */
trait UsesPeak
{
    public function peak(object $class, string $property): mixed
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflector = new ReflectionProperty($class, $property);
        return $reflector->getValue($class);
    }

}