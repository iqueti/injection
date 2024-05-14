<?php

declare(strict_types=1);

namespace Tests\Container\Ioc;

use ArrayObject;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Iquety\Injection\NotFoundException;
use Tests\Ioc\Support\Ioc;
use Tests\TestCase;

class ArgumentsTest extends TestCase
{
    /** @test */
    public function methodWithRequiredArguments(): void
    {
        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        $value = $control->resolve(
            Ioc::class . "::injectedMethodExtraArguments", // <- injeta ArrayObject
            [ "id" => "1", "name" => "Ricardo"] // <- acrescenta  $id + $name
        );
        $this->assertSame([ 'x', 1, "Ricardo" ], $value);
    }

    /** @test */
    public function methodWithoutRequiredArguments(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "It was not possible to resolve the value for parameter (\$name) in method (injectedMethodExtraArguments)"
        );

        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);
        $control->resolve(
            Ioc::class . "::injectedMethodExtraArguments", // <- injeta ArrayObject
            [ "id" => "1", ] // <- acrescenta  $id, mas esquece do $name
        );
    }

    /** @return array<int,array<int,mixed>> */
    public function valuedArgumentsProvider(): array
    {
        return [
            [ array("id" => "1", "name" => "Pereira"), array( 1, "Pereira") ],
            [ array("id" => "1"), array( 1, "Ricardo") ],

            [ array("id" => 1, "name" => "Pereira"), array( 1, "Pereira") ],
            [ array("id" => 1), array( 1, "Ricardo") ],

            [ array("name" => "Pereira"), array( 33, "Pereira") ],
            [ array(), array( 33, "Ricardo") ],
        ];
    }

    /**
     * @test
     * @dataProvider valuedArgumentsProvider
     * @param array<string,string> $arguments
     * @param array<int,mixed> $values
    */
    public function methodWithDefaultValueArguments(array $arguments, array $values): void
    {
        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        $value = $control->resolve(
            Ioc::class . "::injectedMethodExtraDefaultValueArguments", // <- injeta ArrayObject
            $arguments // <- acrescenta  $id + $name
        );

        $this->assertSame(array_merge(["x"], $values), $value);
    }
}
