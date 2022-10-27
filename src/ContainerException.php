<?php

declare(strict_types=1);

namespace Iqueti\Injection;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
