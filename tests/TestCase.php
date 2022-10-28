<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Injection\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Injection\Adapter\Session\MemorySession;
use Iquety\Injection\Application;
use Iquety\Injection\Bootstrap;
use Iquety\Injection\Http\HttpFactory;
use Iquety\Injection\Http\Session;
use Iquety\Injection\Routing\Router;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use ReflectionObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class TestCase extends FrameworkTestCase
{
    public function getPropertyValue(object $instance, string $name): mixed
    {
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }
}
