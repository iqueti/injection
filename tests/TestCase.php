<?php

declare(strict_types=1);

namespace Tests;

use Iqueti\Injection\Adapter\HttpFactory\DiactorosHttpFactory;
use Iqueti\Injection\Adapter\Session\MemorySession;
use Iqueti\Injection\Application;
use Iqueti\Injection\Bootstrap;
use Iqueti\Injection\Http\HttpFactory;
use Iqueti\Injection\Http\Session;
use Iqueti\Injection\Routing\Router;
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
