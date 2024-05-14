<?php

declare(strict_types=1);

namespace Tests\Container;

use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Tests\TestCase;

class RegisterGetArgumentsTest extends TestCase
{
    /** @test */
    public function getSingleton(): void
    {
        $container = new Container();
        $container->addSingleton('myid', fn($one, $two) => $one . $two);

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric', 'ardo'])
        );

        // as próximas chamadas consideram a primeira chamada, ignorando os argumentos
        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['per', 'eira'])
        );

        // chamadas sem argumentos consideram a primeira chamada
        $this->assertEquals(
            'ricardo',
            $container->get('myid')
        );
    }

    /** @test */
    public function getSingletonErrorArguments(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessageMatches('/Too few arguments to function .*/');

        $container = new Container();
        $container->addSingleton('myid', fn($one, $two) => $one . $two);

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric'])
        );
    }

    /** @test */
    public function getFactory(): void
    {
        $container = new Container();
        $container->addFactory('myid', fn($one, $two) => $one . $two);

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric', 'ardo'])
        );

        // todas as chamadas necessitam de argumentos
        $this->assertEquals(
            'pereira',
            $container->getWithArguments('myid', ['per', 'eira'])
        );
    }

    /** @test */
    public function getFactoryErrorOneArgument(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessageMatches('/Too few arguments to function .*/');

        $container = new Container();
        $container->addFactory('myid', fn($one, $two) => $one . $two);

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric', 'ardo'])
        );

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric'])
        );
    }

    /** @test */
    public function getFactoryErrorWithoutArguments(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessageMatches('/Too few arguments to function .*/');

        $container = new Container();
        $container->addFactory('myid', fn($one, $two) => $one . $two);

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('myid', ['ric', 'ardo'])
        );

        $this->assertEquals(
            'ricardo',
            $container->get('myid')
        );
    }

    /** @test */
    public function getNotFoundException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Could not find dependency definition for myid');

        $container = new Container();

        $container->getWithArguments('myid', ['ric', 'ardo']);
    }
}
