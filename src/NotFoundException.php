<?php

declare(strict_types=1);

namespace Iquety\Injection;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
