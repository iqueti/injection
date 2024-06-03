# Container

--page-nav--

## 1. Introdução

O "Container" é um objeto que objetiva centralizar o armazenamento de valores,
possibilitando a consulta posterior por meio de fábricas. É o padrão utilizado 
para Injeção de Dependências e também o principal mecanismo da Inversão de 
Controle. 

Para favorecer interoperabilidade, a implementação cumpre a interface 
[PSR 11](https://www.php-fig.org/psr/psr-11/).

## 2. Tipos de fábricas

Para armazenar valores, deve-se registrar fábricas com `addFactory` e `addSigleton`.
A diferença, entre os dois tipos de fábricas, ocorre no momento que o valor é 
obtido com o método `get`. 

### 2.1. Factory

Valores registrados com `addFactory` serão "fabricados" todas as vezes que `get` 
for invocado. Se `get` for chamado três vezes, o valor será fabricado três vezes.

```php
$container->addFactory('identificacao', $fabrica);
```

### 2.1. Singleton

Os valores registrados com `addSingleton` serão "fabricados" apenas na primeira 
vez que `get` for invocado. Se `get` for chamado três vezes, o valor será fabricado
na primeira vez e será armazedado. As outras duas vezes o mesmo valor previamente 
fabricado será usado.

```php
$container->addSingleton('identificacao', $fabrica);
```

## 3. Usando as fábricas

O segundo argumento deve ser a fábrica a ser registrada. Ela pode ser especificada 
de várias formas:

### 3.1. Valor direto

O valor adicionado diretamente não serão fabricados, pois já serão armazenados na 
hora de sua definição.

```php
$container->addSingleton('identificacao', new MinhaClasse());

// retorna instância de MinhaClasse
$container->get('identificacao'); 
```

### 3.2. Callback

Se uma função anônima for fornecida, ela será executada somente quando o método 
`get` for invocado.

```php
$container->addSingleton('identificacao', function() {
    return new MinhaClasse();
});

// retorna instância de MinhaClasse
$container->get('identificacao'); 
```

Um callback também pode receber argumentos de `getWithArguments`:

```php
$container->addSingleton('identificacao', function($argumentoUm) {
    return new MinhaClasse($argumentoUm);
});

// retorna instância de MinhaClasse
$container->getWithArguments('identificacao', ['valorDoArgumento']); 
```

### 3.3. Assinatura

Se o nome completo de uma classe sem construtor for fornecido, ela será 
instanciada somente quando o método `get` for invocado.

```php
$container->addSingleton('identificacao', MinhaClasse::class);

// retorna instância de MinhaClasse
$container->get('identificacao'); 
```

Se o identificador usado for o mesmo nome completo da classe a ser fabricada,
não será necessário especificar o segundo argumento. Ao invocar o método `get`, 
a classe será instanciada.

```php
$container->addSingleton(MinhaClasse::class);

// retorna instância de MinhaClasse
$container->get('identificacao'); 
```

O nome de uma classe também pode ser fabricada fornecendo argumentos com `getWithArguments`:

```php
$container->addSingleton('identificacao', MinhaClasse::class);

// retorna instância de MinhaClasse, recebendo 'valorDoArgumento'
$container->getWithArguments('identificacao', ['valorDoArgumento']); 
```

## 4. Verificando a existência

Para verificar se um valor já foi registrado, usa-se o método `has`.

```php
$container->addSingleton('identificacao', MinhaClasse::class);

// retorna true se a identificação tiver sido registrada
$container->has('identificacao'); 
```

--page-nav--
