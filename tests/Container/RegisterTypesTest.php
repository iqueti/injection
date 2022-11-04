<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Exception;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Iquety\Injection\NotFoundException;
use Tests\TestCase;

class RegisterTypesTest extends TestCase
{
    /** @test */
    public function registerFactory(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->registerDependency('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    public function registerSingleton(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->registerSingletonDependency('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    /** @test */
    public function registerArbiraryValue(): void
    {
        $container = new Container();

        $container->registerDependency('mystring', 'parangarikotirimirruaro');
        $this->assertEquals('parangarikotirimirruaro', $container->get('mystring'));

        $container->registerDependency('myint', 11);
        $this->assertEquals(11, $container->get('myint'));

        $container->registerDependency('myfloat', 11.22);
        $this->assertEquals(11.22, $container->get('myfloat'));

        $array = ['one', 'two', 'three'];
        $container->registerDependency('myarray', $array);
        $this->assertEquals($array, $container->get('myarray'));

        $object = (object)['one', 'two', 'three'];
        $container->registerDependency('myobject', $object);
        $this->assertEquals($object, $container->get('myobject'));
    }

    /** @test */
    public function registerClosure(): void
    {
        $container = new Container();
        $container->registerDependency('id', fn() => "kkk");
        $this->assertEquals('kkk', $container->get('id'));
    }

    /** @test */
    public function registerObjectContract(): void
    {
        $container = new Container();
        $container->registerDependency('id', ArrayObject::class);
        $this->assertInstanceOf(ArrayObject::class, $container->get('id'));
    }

    /** @test */
    public function registerFunction(): void
    {
        $container = new Container();
        $container->registerDependency('id', 'microtime');

        $this->assertNotFalse(preg_match('#[0-9]{1}.[0-9]* [0-9]*#', $container->get('id')));
    }

    /** @test */
    public function registerOnlyContract(): void
    {
        $container = new Container();
        
        $container->registerDependency(ArrayObject::class);
        $this->assertEquals(new ArrayObject(), $container->get(ArrayObject::class));
    }
}
