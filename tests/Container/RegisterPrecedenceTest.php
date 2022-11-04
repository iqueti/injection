<?php

declare(strict_types=1);

namespace Tests\Container;

use Iquety\Injection\Container;
use Tests\TestCase;

class RegisterPrecedenceTest extends TestCase
{
    /** @test */
    public function registerSingletonAndFactory(): void
    {
        $container = new Container();

        // registra uma dependência singleton
        $container->registerSingletonDependency('myid', 'parangarikotirimirruaro');

        // obtém a listas de dependências registradas
        $singletonValues = $this->getPropertyValue($container, 'singleton');
        $factoryValues = $this->getPropertyValue($container, 'factory');

        $this->assertCount(1, $singletonValues);
        $this->assertCount(0, $factoryValues);
        $this->assertTrue(in_array('parangarikotirimirruaro', $singletonValues));

        // tenta registrar uma fábrica com mesmo $id do singleton
        $container->registerDependency('myid', 'sobrescreve');

        // obtém a listas de dependencias registradas
        $singletonValues = $this->getPropertyValue($container, 'singleton');
        $factoryValues = $this->getPropertyValue($container, 'factory');

        // ao invés de registrar a factory, o valor foi sobrescrito na dependência singleton
        // porque o uso de um singleton precede o uso de factories
        $this->assertCount(1, $singletonValues);
        $this->assertCount(0, $factoryValues);
        $this->assertTrue(in_array('sobrescreve', $singletonValues));
    }

    /** @test */
    public function registerFactoryAndSingleton(): void
    {
        $container = new Container();

        // registra uma factory
        $container->registerDependency('id', 'parangarikotirimirruaro');

        // obtém a listas de dependências registradas
        $singletonValues = $this->getPropertyValue($container, 'singleton');
        $factoryValues = $this->getPropertyValue($container, 'factory');

        $this->assertCount(0, $singletonValues);
        $this->assertCount(1, $factoryValues);
        $this->assertTrue(in_array('parangarikotirimirruaro', $factoryValues));
        
        // tenta registrar um singleton com mesmo $id do factory
        $container->registerSingletonDependency('id', 'sobrescreve');

        // dependência é removida da fábrica e alocada como singleton
        // porque o uso de um singleton precede o uso de factories
        $singletonValues = $this->getPropertyValue($container, 'singleton');
        $factoryValues = $this->getPropertyValue($container, 'factory');

        $this->assertCount(1, $singletonValues);
        $this->assertCount(0, $factoryValues);
        $this->assertTrue(in_array('sobrescreve', $singletonValues));
    }
}
