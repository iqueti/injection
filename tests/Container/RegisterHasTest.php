<?php

declare(strict_types=1);

namespace Tests\Container;

use Iquety\Injection\Container;
use Tests\TestCase;

class RegisterHasTest extends TestCase
{
    /** @test */
    public function hasFactory(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->registerDependency('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    public function hasSingleton(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->registerSingletonDependency('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }
}
