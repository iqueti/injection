<?php

declare(strict_types=1);

namespace Iqueti\Injection;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
