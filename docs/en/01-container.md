# Container

[◂ Documentation index](index.md) | [Inversion of Control ▸](02-inversion-of-control.md)
-- | --

## 1. Introduction

The "Container" is an object that aims to centralize the storage of values, 
enabling subsequent consultation through factories. This is the standard used
for Dependency Injection and also the main mechanism for Inversion of Control.

To promote interoperability, the implementation complies with the interface
[PSR 11](https://www.php-fig.org/psr/psr-11/).

## 2. Types of factories

To store values, you must register factories with `addFactory` and `addSigleton`.
The difference between the two types of factories occurs when the value is
obtained with the `get` method.

### 2.1. Factory

Values ​​registered with `addFactory` will be "manufactured" every time `get` is 
invoked. If `get` is called three times, the value will be manufactured three times.

```php
$container->addFactory('identification', $factory);
```

### 2.1. Singleton

Values ​​registered with `addSingleton` will only be "manufactured" on the first
time `get` is invoked. If `get` is called three times, the value will be fabricated
the first time and will be stored. The other two times the same value previously
manufactured will be used.

```php
$container->addSingleton('identification', $factory);
```

## 3. Using the factories

The second argument must be the factory to register. It can be specified
in several ways:

### 3.1. Direct value

The value added directly will not be manufactured, as it will already be stored in the
time of its definition.

```php
$container->addSingleton('identification', new MyClass());

// returns instance of MyClass
$container->get('identification'); 
```

### 3.2. Callback

If an anonymous function is provided, it will be executed only when the method
`get` is invoked.

```php
$container->addSingleton('identification', function() {
    return new MyClass();
});

// returns instance of MyClass
$container->get('identification'); 
```

A callback can also receive arguments from `getWithArguments`:

```php
$container->addSingleton('identification', function($argumentoUm) {
    return new MyClass($argumentoUm);
});

// returns instance of MyClass
$container->getWithArguments('identification', ['argumentValue']); 
```

### 3.3. Signature

If the full name of a class without a constructor is given, it will be
instantiated only when the `get` method is invoked.

```php
$container->addSingleton('identification', MyClass::class);

// returns instance of MyClass
$container->get('identification'); 
```

If the identifier used is the same full name as the class to be crafted, it will 
not be necessary to specify the second argument. When invoking the `get` method,
the class will be instantiated.

```php
$container->addSingleton(MyClass::class);

// returns instance of MyClass
$container->get('identification'); 
```

A class name can also be fabricated by providing arguments with `getWithArguments`:

```php
$container->addSingleton('identification', MyClass::class);

// returns instance of MyClass, receiving 'argumentValue'
$container->getWithArguments('identification', ['argumentValue']); 
```

## 4. Checking for existence

To check whether a value has already been registered, the `has` method is used.

```php
$container->addSingleton('identification', MyClass::class);

// returns true if the identification has been registered
$container->has('identification'); 
```

[◂ Documentation index](index.md) | [Inversion of Control ▸](02-inversion-of-control.md)
-- | --
