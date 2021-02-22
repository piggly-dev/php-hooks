# Crie ganchos para execuções de códigos em suas aplicações

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piggly/php-hooks.svg?style=flat-square)](https://packagist.org/packages/piggly/php-hooks) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) 

> Essa biblioteca foi inspirada nas funções `do_action` e `apply_filter` disponibilizadas no núcleo do **[Wordress](https://developer.wordpress.org/plugins/hooks/)**. 

Os **Ganchos** (`Hooks`) são uma forma de executar determinados trecos de códigos em pontos específicos predefinidos. A principal vantagem desses recursos é acumular uma série de funções que um `Hook` irá executar quando for solicitado.

Nessa biblioteca, em contra ponto a proposta do Wordpress, existem três tipos de ganchos: *filtros, ações e disparadores*. Todos eles, registram um `callback` com ou sem parâmetros a uma tag. E, posteriormente, essa tag poderá ser executada.

> Se você apreciar a função desta biblioteca e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ou para a carteira Bitcoin `3DNssbspq7dURaVQH6yBoYwW3PhsNs8dnK` ❤.

## Instalação

Essa biblioteca pode ser instalada via **Composer** com `composer require piggly/php-hooks`;
 
## Registrando funções

Enquanto uma tag não for disparada, é possível adicionar e/ou remover funções registradas nela. E, para cada função registrada é possível criar uma tag composta por: *nome da tag, nome da função, argumentos requeridos e prioridade de execução*.

As tags são formadas de duas formas: utilizando a função `Syntax::create()` ou escrevendo uma `string` utilizando a sintaxe da tag.

A sintaxe de uma tag é composta por:

* `{tagname}`: **(Obrigatório)** Indicando o nome da tag;
* `.{function_name}`: Indicando um nome da função;
* `?{args}`: Número de argumentos a serem recebidos pela função;
* `::{priority}`: Numéro da prioridade de execução da função;

> O nome dado a uma função sempre deverá ser único. Caso o nome da função já exista na Tag será retornada a exceção `NameAlreadyExistsException`.

O regex aplicado na sintaxe é `/^(?:(?P<tag>[^\.\:\?]+))(?:\.(?P<name>[^\:\?]+))?(?:\?(?P<args>[\d]+))?(?:\:\:(?P<priority>[\d]+))?$`. Quando a sintaxe da tag estiver incorreta, a exceção `InvalidSyntaxException` será retornada.

Por exemplo:

* A função deverá ser registrada com a tag `calculate` e prioridade `1`, o resultado da sintaxe será: `calculate::1`;
* A função deverá ser registrada com a tag `calculate`, nome `pow` e com `2` argumento requeridos, o resultado da sintaxe será: `calculate.pow?2`;

Veja abaixo outros exemplos de tags válidas:

```php
// Registra a função na tag calculate
$tag = 'calculate';

// Registra a função na tag calculate com dois argumentos requeridos
$tag = 'calculate?2';

// Registra a função na tag calculate com prioridade 1
$tag = 'calculate::1';

// Registra a função na tag calculate com dois argumentos requeridos e prioridade 1
$tag = 'calculate?2::1';

// Registra a função na tag calculate com nome sum
$tag = 'calculate.sum';

// Registra a função na tag calculate com nome sum e dois argumentos requeridos
$tag = 'calculate.pow?2';

// Registra a função na tag calculate com nome sum e prioridade 1
$tag = 'calculate.sum::1';

// Registra a função na tag calculate com nome sum, dois argumentos requeridos e prioridade 1
$tag = 'calculate.pow?2::1';
```

## Callbacks

Toda função registrada, além de exigir uma tag, também exige um `callback`. Os `callbacks` que podem ser recebidos pelas funções `Hook::filter()`, `Hook::action()` e  `Hook::dispatch()` são:

* `Closure`: um objeto do tipo closure `Hook::filter( $tagSyntax, function ( $number ) { return $number + 15; } );`
* `Function`: uma `string` referênciando o nome da função `Hook::filter( $tagSyntax, 'sum' );`
* `Static Object`: uma `class` com uma `static function` referênciando o nome da classe e do método estático `Hook::filter( $tagSyntax, StaticClass::class, 'methodToCall' );`
* `Object`: uma `instance` de um objeto referênciando o nome do método a ser chamado `Hook::filter( $tagSyntax, $instance, 'methodToCall' );`

### Parâmetros adicionais

Após a referênciação do `callback` os últimos parâmetros sempre serão os parâmetros adicionais anexados aquela função. Por exemplo, `Hook::dispatch( $tagSyntax, 'sum', 1 );` terá como parâmetro adicional `1`. Os parâmetros adicionais são considerados apenas na função `Hook::dispatch()`.

> Entenda mais sobre `Hook::filter()`, `Hook::action()` e  `Hook::dispatch()` abaixo.

## Filtros x Ações x Disparadores

A principal diferença entre esses três tipos de ganchos são:

* Um filto pega as informações que recebe no ponto de execução, ,modifica-as de alguma forma e as retorna. Em outras palavras: modifica algo e devolve ao ganho para uso no próximo filtro e assim sucessivamente;

> Um exemplo de filtro é o filtro `Hook::apply('comment', $comment)` que filtra o comentário removendo palavras ilegais, removendo links, etc.

* Uma ação pega as informações que recebe no ponto de execução, faz algo com elas e não retornada nada. Em outras palavras: executa algo e depois encerra sua atuação com começo, meio e fim.

> Um exemplo de ação é uma notificação ao criar um novo ticket `Hook::run('new_ticket', $ticket)`. A ação receberá `$ticket` e poderá enviar uma notificação de acordo.

* Um disparador é como uma ação, mas ao invés de pegar as informações que recebe no ponto de execução, pega as informações no ponto de registro. Em outras palavras: executa algo não relacionado ao ponto de execução, mas que interfere nele. 

> Um exemplo de disparador é incluir um arquivo CSS na HTML tag <head> `Hook::run('head')`. Nesse caso, o disparador irá receber o `$name` do arquivo CSS no momento em que ele é registrado `Hook::dispatch('head', 'importCss', 'home.css')`.

## Filtros `Hook::filter()`

Os **filtros** concedem aos ganchos a habilidade de manipular dados durante a sua execução. As funções do filtro irão receber um variável, modificá-la e retorná-la. As funções registradas em um filtro devem funcionar de maneira isolada e nunca devem ter efeitos colaterais. Os filtros sempre esperam que algo seja devolvido a eles.

Para registrar uma nova função em um filtro o método `Hook::filter()` deve ser adicionado considerando os seguintes parâmetros:

* `$tagSyntax`: Uma sintaxe da tag onde a função será registrada;
* `$callback, ?$method`: A função a ser executada.

Para aplicar um filtro o método `Hook::apply()` deve ser adicionado considerando os seguintes parâmetros:

* `$tag`: Nome da tag que será executada;
* `$value`: O valor inicial do filtro;
* `...$params`: Os parâmetros adicionais do filtro;

Além disso, existem duas variações do método `Hook::apply()`, são elas:

* `Hook::applyByName()` executa apenas uma função específica de uma tag;
* `Hook::applyOnce()` executa apenas uma vez a tag, depois disso, ela não poderá mais ser utilizada;

Também é possível remover filtros antes da execução com o método `Hook::removeFilter()`.

> **Dica**: Utilize o método `Hook::applyOnce()` quando uma tag de filtro for utilizada apenas uma vez durante o ciclo de vida da sua aplicação, isso liberará espaço na memória durante o tempo de execução.

Confira abaixo um exemplo prático do uso de filtros:

```php
function fsum ( $number ) { return $number+15; }
function fsub ( $number ) { return $number - 10; }
function fmul ( $number ) { return $number * 3; }
function fdiv ( $number ) { return $number / 2; }
function fpow ( $number, $exp ) { return is_numeric($exp) ? pow($number, $exp) : $number; }

Hook::filter('calculate.sum', 'fsum');
Hook::filter('calculate.sub', 'fsub');
Hook::filter('calculate.mul', 'fmul');
Hook::filter('calculate.div', 'fdiv');

// -> Apply => Expects (((10+15)-10)*3)/2 = 22.5
$number = Hook::apply('calculate', 10);
echo sprintf("Number: %s\n", $number);
```

Os exemplos completos estão disponíveis [aqui](samples/filters.php).

## Ações `Hook::action()`

As **ações** permitem modificar o comportamento da sua aplicação. As funções de uma ação podem escrever alguma saída, inserir dados no banco de dados, enviar notificações e afins. As funções registradas em uma ação devem sempre performar algum tipo de tarefa, por essa razão nenhum tipo de retorno da função acontecerá.

Para registrar uma nova função em uma ação o método `Hook::action()` deve ser adicionado considerando os seguintes parâmetros:

* `$tagSyntax`: Uma sintaxe da tag onde a função será registrada;
* `$callback, ?$method`: A função a ser executada.

Para executar uma ação o método `Hook::run()` deve ser adicionado considerando os seguintes parâmetros:

* `$tag`: Nome da tag que será executada;
* `...$params`: Os parâmetros adicionais da ação;

Além disso, existem duas variações do método `Hook::run()`, são elas:

* `Hook::runByName()` executa apenas uma função específica de uma tag;
* `Hook::runOnce()` executa apenas uma vez a tag, depois disso, ela não poderá mais ser utilizada;

Também é possível remover ações antes da execução com o método `Hook::removeAction()`.

> **Dica**: Ações geralmente devem ser executadas apenas uma vez, então sempre que esse for o comportamento esperado utilize `Hook::runOnce()`, isso liberará espaço na memória durante o tempo de execução.

Confira abaixo um exemplo prático do uso de ações:

```php
function line () { echo "I am line 02\n"; }
function message ( $message ) { echo sprintf("Message: %s\n", $message); }

Hook::action('sentences.line', 'line');
Hook::action('sentences.message', 'message');

Hook::run('sentences', 'Peace and Love');
```

Os exemplos completos estão disponíveis [aqui](samples/actions.php).

## Disparadores `Hook::dispatch()`

Os **disparadores** possuem o mesmo comportamento que as ações. Executam trechos de códigos sem nenhum retorno e são registrados pelo método `Hook::dispatch()`. Entretanto, possuem como principal diferença o recebimento dos parâmetros adicionais. 

Considere que na sua aplicação exista um gancho `head` que será executado para incluir HTML tags na tag `<head>` da sua aplicação. 

```html
<html>
    <head>
        <?php Hook::run('head'); ?>
    </head>
    <body>
    </body>
</html>
```

Vamos supor que, então, você tenha o `Controller` que inclui os métodos `home()`, `services()` e `about()` (um equivalente para cada página). E, ainda, tem os arquivos `home.css`, `services.css` e `about.css`. Para incluí-los na tag `head`, será preciso criar uma função para cada página. Veja:

```php
class Controller
{
    // ...

    public function home()
    {
        Hook::action('head', $this, 'homeAction');
        // ...
    }

    public function services()
    {
        Hook::action('head', $this, 'servicesAction');
        // ...
    }

    public function about()
    {
        Hook::action('head', $this, 'aboutAction');
        // ...
    }

    public function homeAction ()
    { /** Add css file... **/ }

    public function servicesAction ()
    { /** Add css file... **/ }

    public function aboutAction ()
    { /** Add css file... **/ }

    // ...
}
```

Muito chato não? Isso acontece porque o método `Hook::run('head')` não envia parâmetros para as ações e as ações não recebem parâmetros. Também não é possível colocar em `Hook::run('head')` os parâmetros que somente os métodos `home()`, `services()` e `about()` exigem.

Os disparadores existem para resolver esse problema. Neste caso, imagine que tenhamos a função `importCss($name)`. Essa função incluí a tag `<link>` na página de acordo com o `$name` do arquivo. Agora, tudo que precisamos fazer é registrar o disparo dessa função com o parâmetro `$name`. Veja:


```php
class Controller
{
    // ...

    public function home()
    {
        Hook::dispatch('head', 'importCss', 'home.css');
        // ...
    }

    public function services()
    {
        Hook::dispatch('head', 'importCss', 'services.css');
        // ...
    }

    public function about()
    {
        Hook::dispatch('head', 'importCss', 'about.css');
        // ...
    }

    // ...
}
```

Agora reduzimos nosso código e trabalhamos com ganchos ainda mais eficientes. Com os disparadores podemos incluir funções nos códigos de forma mais inteligente sem depender de funções equivalentes a ação que estamos criando (uma vez que ações não permitem parâmetros adicionais fora do ponto de execução `Hook::run()`).

É muito importante compreender que um gancho `Hook::run('new_ticket', $ticket)` está enviando o parâmetro `$ticket` e esse parâmetro é recebido por qualquer `Hook::action('new_ticket.notification', 'notification');` entretanto não é recebido por um `Hook::dispatch('new_ticket.notification', 'notification', $ticket);` que deve incluir `$ticket` durante o seu registro.

> Ações devem ser utilizadas quando precisam herdar os parâmetros da execução `Hook::run()`, já os disparadores devem ser utilizados quando queremos incluir uma função com seus próprios parâmetros no gancho.

Também é possível remover disparadores antes da execução com o método `Hook::removeDispatcher()`.

Confira abaixo um exemplo prático do uso de disparadores:

```php
function name ( $message ) { echo sprintf("Your name: %s\n", $message); }
function prog ( $message ) { echo sprintf("Progamming Language: %s\n", $message); }

Hook::dispatch('sentences.name::1', 'name', 'Alpha');
Hook::dispatch('sentences.prog::1', 'prog', 'JS');

Hook::run('sentences', 'Peace and Love');
```

Os exemplos completos estão disponíveis [aqui](samples/dispatchers.php).

## Changelog

Veja o arquivo [CHANGELOG](CHANGELOG.md) para informações sobre todas as mudanças no código.

## Testes de Código

Essa biblioteca utiliza o [PHPUnit](https://phpunit.de/). Realizamos testes com todas as principais classes dessa aplicação.

```
vendor/bin/phpunit
```

## Contribuições

Veja o arquivo [CONTRIBUTING](CONTRIBUTING.md) para informações antes de enviar sua contribuição.

## Segurança

Se você descobrir qualquer issue relacionada a segurança, por favor, envie um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br) ao invés de utilizar o rastreador de issues do Github.

## Créditos

- [Caique Araujo](https://github.com/caiquearaujo)
- [Todos os colaboradores](../../contributors)

## Apoie o projeto

**Piggly Studio** é uma agência localizada no Rio de Janeiro, Brasil. Se você apreciar a função desta biblioteca e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

## License

MIT License (MIT). Veja [LICENSE](LICENSE) para mais informações.