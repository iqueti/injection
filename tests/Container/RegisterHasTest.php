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

        $container->addFactory('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    /** @test */
    public function hasSingleton(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->addSingleton('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }
}
