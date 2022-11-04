<?php

declare(strict_types=1);

namespace Iquety\Injection;

use Closure;
use InvalidArgumentException;
use Iquety\Injection\ContainerException;
use Iquety\Injection\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Reflector;

class InversionOfControl
{
    private string $forceInstanceOf = '';

    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * Invoca um objeto ou classe através do container.
     * string: Controller::action
     * array: [Controller, action]
     * object: new Controller()
     * callable: "Controller" ou function() {}
     * @param string|array<string,string>|object|callable $callable
     * @param array<string,mixed> $arguments
     * @return mixed
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolve(string|array|object|callable $callable, array $arguments = []): mixed
    {
        $this->forceInstanceOf = '';

        return $this->resolveRaw($callable, $arguments);
    }

    /**
     * Invoca um objeto do tipo $instanceContract através do container.
     * string: Controller::action
     * array: [Controller, action]
     * object: new Controller()
     * callable: "Controller" ou function() {}
     * @param string $instanceContract 
     * @param string|array<string,string>|object|callable $callable
     * @param array<string,mixed> $arguments
     * @return mixed
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolveOnly(
        string $allowedContract,
        string|array|object|callable $callable,
        array $arguments = []
    ): mixed {

        $this->forceInstanceOf = $allowedContract;

        return $this->resolveRaw($callable, $arguments);
    }

    private function resolveRaw(string|array|object|callable $callable, array $arguments = []): mixed
    {
        $info = $this->callableInfo($callable);

        return match($info['type']){
            'class' => $this->invokeClass($info['wrapper'], $info['callable'], $arguments),
            'method' => $this->invokeMagic($info['wrapper'], $info['callable'], $arguments),
            'function' => $this->invokeFunction(\Closure::fromCallable($info['callable']), $arguments)
        };
    }

    /**
     * @return array<string,mixed>
     * @throws ContainerException para injeções impossíveis
     */
    private function callableInfo(string|array|object|callable $callable): array
    {
        $info = [
            'type'     => 'unresolved',
            'wrapper'  => '',
            'callable' => '',
        ];

        if (is_string($callable) === true && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) === true) {
            $info['type'] = 'class';
            $info['wrapper'] = $callable[0];
            $info['callable'] = $callable[1];

            return $info;
        }

        if (is_object($callable) === true) {
            $info['type'] = 'method';
            $info['wrapper'] = $callable;
            $info['callable'] = '__invoke';

            return $info;
        }

        if (is_callable($callable) === false) {
            throw new ContainerException("Impossible to inject " . gettype($callable) . " dependency");
        }

        $info['type'] = 'function';
        $info['callable'] = $callable;

        return $info;
    }

    private function assertContract(ReflectionClass|string $reflection): void
    {
        if ($this->forceInstanceOf === '') {
            return;
        }
        
        // para funções, pois não possuem contratos
        if (is_string($reflection) === true) {
            throw new InvalidArgumentException(
                sprintf('Type %s do not have contracts', $reflection)
            );
            return;
        }

        if (
            $reflection->getName() !== $this->forceInstanceOf // a propria classe
            && $reflection->isSubclassOf($this->forceInstanceOf) === false // extends | implements
        ) {
            throw new InvalidArgumentException(
                sprintf('Class type %s is not allowed', $reflection->getName())
            );
        }
    }

    /**
     * @param object|class-string $objectOrClass
     * @param array<string,mixed> $arguments
     * @return mixed
    */
    private function invokeClass(object|string $objectOrClass, string $methodName, array $arguments)
    {
        // injeta dependencias no construtor
        $reflection = new ReflectionClass($objectOrClass);

        $this->assertContract($reflection);

        $construct = $reflection->getConstructor();

        $resolution = $construct === null
            ? new $objectOrClass()
            : $reflection->newInstanceArgs($this->argumentsInjected($construct, $arguments));

        return $this->invokeMagic($resolution, $methodName, $arguments);
    }

    /**
     * @param array<string,mixed> $arguments
     * @return mixed
    */
    private function invokeMagic(object $object, string $method, array $arguments)
    {
        $reflection = new ReflectionMethod($object, $method);

        $this->assertContract($reflection->getDeclaringClass());

        if ($reflection->isStatic() === true) {
            $object = null;
        }

        return $reflection->invokeArgs(
            $object,
            $this->argumentsInjected($reflection, $arguments)
        );
    }

    /** @param array<string,mixed> $arguments */
    private function invokeFunction(Closure $function, array $arguments): mixed
    {
        $reflection = new ReflectionFunction($function);

        $this->assertContract($reflection->getName());

        return $reflection->invokeArgs(
            $this->argumentsInjected($reflection, $arguments)
        );
    }

    /**
     * @param array<string,mixed> $arguments
     * @return array<int,mixed>
    */
    private function argumentsInjected(ReflectionMethod|ReflectionFunction $reflection, array $arguments): array
    {
        $reflect = function (ReflectionParameter $param) use ($reflection, $arguments) {
            $name = $param->getName();
            $type = $param->getType();

            // argumentos passados coincidem com parâmetros declarados no método
            if (isset($arguments[$name]) === true) {
                return $arguments[$name];
            }

            $dependencyId = (string) $type;
            $dependencyId = ltrim($dependencyId, '?');
            if ($type !== null && $this->container->has($dependencyId) === true) {
                return $this->container->get($dependencyId);
            }

            if ($param->isDefaultValueAvailable() === true) {
                return $param->getDefaultValue();
            }

            throw new NotFoundException(sprintf(
                'It was not possible to resolve the value for parameter ($%s) in method (%s)',
                $name,
                $reflection->getName()
            ));
        };

        return array_map($reflect, $reflection->getParameters());
    }
}
