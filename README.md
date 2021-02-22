# Create hooks for run pieces of code in your application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piggly/php-hooks.svg?style=flat-square)](https://packagist.org/packages/piggly/php-hooks) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) 

> Para ler esse arquivo em português [clique aqui](README.pt_BR.md)

> This library was inspired by the functions `do_action` and `apply_filter` available at **[Wordress](https://developer.wordpress.org/plugins/hooks/)** core. 

Hooks are a way to run certain piece of code at specific, pre-defined spots. The main avantage of this feature is that hook can accumulates a bunch of functions that will be performed only when requested.

In this library, in contrast to the Wordpress proposal, there are three types of hooks: *filters, actions and dispatchers*. All of them register a `callback`, including or not parameters, to a tag. And, later, this tag can be executed.

> If you like this library and want to support this job, be free to donate any value to BTC wallet `3DNssbspq7dURaVQH6yBoYwW3PhsNs8dnK` ❤.

## Installation

This library can be installed by using **Composer** with `composer require piggly/php-hooks`;
 
## Registering callbacks

As long as a hook tag is not fired, it is possible to add and/or remove callbacks registered to it. And, for each registered callback, it is possible to create a tag composed by: *tag name, function name, required arguments and execution priority*.

Tags are formed by two ways: using the `Syntax::create()` function or writing a `string` by using tag syntax below.

The syntax of a tag consists of:

* `{tagname}`: **(Required)** Tag name;
* `.{function_name}`: Function name;
* `?{args}`: Number of arguments to be received, by default always will be `1`;
* `::{priority}`: Number of execution priority, by default always will be `10`;

> A name gave to a callback always needs to be unique. If function name already exists in tag, it will throws `NameAlreadyExistsException`.

The tag syntax regex is `/^(?:(?P<tag>[^\.\:\?]+))(?:\.(?P<name>[^\:\?]+))?(?:\?(?P<args>[\d]+))?(?:\:\:(?P<priority>[\d]+))?$`. If tag syntax is wrong, it will throws `InvalidSyntaxException`.

For example:

* To register a callback with tag `calculate` and priority `1`, tag syntax will be: `calculate::1`;
* To register a callback with tag `calculate`, name `pow` and `2` arguments to be received, tag syntax will be: `calculate.pow?2`;

Check below another examples:

```php
// Register in tag calculate
$tag = 'calculate';

// Register in tag calculate ready to recieve two args
$tag = 'calculate?2';

// Register in tag calculate with priority 1
$tag = 'calculate::1';

// Register in tag calculate ready to recieve two args and priority 1
$tag = 'calculate?2::1';

// Register in tag calculate with name sum
$tag = 'calculate.sum';

// Register in tag calculate with name sum ready to recieve two args
$tag = 'calculate.pow?2';

// Register in tag calculate with name sum and priority 1
$tag = 'calculate.sum::1';

// Register in tag calculate with name sum, ready to recieve two args and priority 1
$tag = 'calculate.pow?2::1';
```

## Callbacks

Any registred function, in addition to requiring a tag, it also requires a `callback`. The callbacks that can be received by the `Hook::filter()`, `Hook::action()` and `Hook::dispatch()` methods are:

* `Closure`: a clousure object type `Hook::filter( $tagSyntax, function ( $number ) { return $number + 15; } );`
* `Function`: a `string` referring to the function name `Hook::filter( $tagSyntax, 'sum' );`
* `Static Object`: a `class` with a `static function` referring to the class name and static method `Hook::filter( $tagSyntax, StaticClass::class, 'methodToCall' );`
* `Object`: an `instance` of an object referring to object method to be called `Hook::filter( $tagSyntax, $instance, 'methodToCall' );`

### Additional parameters

After referencing the `callback`, the last parameters will always be the additional parameters attached to that function. For example, `Hook::dispatch( $tagSyntax, 'sum', 1 );` will have an additional parameter as `1`. Additional parameters are used only in the `Hook::dispatch()` method.

> Understand more about `Hook::filter()`, `Hook::action()` and `Hook::dispatch()` below.

## Filters x Actions x Dispatchers

The main difference between these three types of hooks are:

* A filter takes the information it receives at the point of execution, modifies it in some way and returns it. In other words: modify something and return for use in the next filter, and so on;

> An example of a filter is the filter `Hook::apply('comment', $comment)` which filters the comment by removing illegal words, removing links, etc.

* An action takes the information it receives at the point of execution, does something with it and returns nothing. In other words: perform something and then end your code with a beginning, middle and end.

> An example of an action is a notification when a new ticket is created `Hook::run('new_ticket', $ticket)`. The action will receive `$ticket` and it can send a notification accordingly.

* A dispatcher is like an action, but instead of taking the information it receives at the point of execution, it takes the information at the point of registration. In other words: it executes something not related to the execution point, but that interferes on it. 

> An example of a dispatcher is to include a CSS file in the HTML tag <head> `Hook::run('head')`. Here, dispatcher will receive CSS `$name` file at moment it is registered `Hook::dispatch('head', 'importCss', 'home.css')`.

## Filters `Hook::filter()`

**Filters** give hooks the ability to manipulate data during execution. The filter functions will receive a variable, modify it and return it. The functions registered in a filter must work isolated and should never have side effects. Filters always expect something to be returned to them.

To register a new callback to a filter, the method `Hook::filter()` should be called with the following parameters:

* `$tagSyntax`:A tag syntax where the function will be registered;
* `$callback, ?$method`: The callback to be executed.

To apply a filter, the method `Hook::apply()` should be called with the following parameters:

* `$tag`: Tag name that will be executed;
* `$value`: Initial value to filter;
* `...$params`: Additional parameters to filter (`Hook::filter()` need to be able to receive this parameters in the tag syntax with `?{args}`, by default always will be `1`);

In addition, there are two variations of the `Hook::apply()` method, they are:

* `Hook::applyByName()` executes only a specific function of a tag;
* `Hook::applyOnce()` executes the tag only once, after that, it can no longer be used;

It is also possible to remove filters before execution with the method `Hook::removeFilter()`.

> **Tip**: Use the `Hook::applyOnce()` method when a filter tag is used only once during the life cycle of your application, it will free up memory space during run time.

Check out a practical example of using filters below:

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

Full examples are available [here](samples/filters.php).

## Actions `Hook::action()`

**Actions** allow you to modify the behavior of your application. The functions of an action can write some output, insert data into the database, send notifications and the so on. The functions registered in an action must always perform some type of task, for this reason no type of return will happen.

To register a new callback to an action, the method `Hook::action()` should be called with the following parameters:

* `$tagSyntax`:A tag syntax where the function will be registered;
* `$callback, ?$method`: The callback to be executed.

To run an action, the method `Hook::run()` should be called with the following parameters:

* `$tag`: Tag name that will be executed;
* `...$params`: Additional parameters to action (`Hook::action()` need to be able to receive this parameters in the tag syntax with `?{args}`, by default always will be `1`);

In addition, there are two variations of the `Hook::run()` method, they are:

* `Hook::runByName()` executes only a specific function of a tag;
* `Hook::runOnce()` executes the tag only once, after that, it can no longer be used;

It is also possible to remove actions before execution with the method `Hook::removeAction()`.

> **Tip**: Use the `Hook::runOnce()` method when an action tag is used only once during the life cycle of your application, it will free up memory space during run time.

Check out a practical example of using actions below:

```php
function line () { echo "I am line 02\n"; }
function message ( $message ) { echo sprintf("Message: %s\n", $message); }

Hook::action('sentences.line', 'line');
Hook::action('sentences.message', 'message');

Hook::run('sentences', 'Peace and Love');
```

Full examples are available [here](samples/actions.php).

## Dispatchers `Hook::dispatch()`

**Dispatchers** have the same behavior as actions. They execute code snippets without any return and are registered by the `Hook::dispatch()` method. However, the main difference how they receive the additional parameters.

Consider that in your application there is a hook `head` that will run to add HTML tags into `<head>` tag of your application. 

```html
<html>
    <head>
        <?php Hook::run('head'); ?>
    </head>
    <body>
    </body>
</html>
```

Let's assume, then, that you have the `Controller` which includes the methods `home()`, `services()` and `about()` (one equivalent for each page). And yet, there are the files `home.css`, `services.css` and `about.css`. To include them into the `head` that, it is required to create one callback to each action. See:

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

Too boring isn't it? This happens because the method `Hook::run('head')` does not send parameters for actions and actions do not receive parameters. That's because we need one callback by action. It is also not possible to send the required parameters of `home()`, `services()` e `about()` in the point of execution `Hook::run('head')`.

The dispatchers were created to solve this problem. In this case, imagine that we have the function `importCss($name)`. This function echo the `<link>` tag in the page by using `$name` as CSS name file. Now, all we need to do is register the dispatcher of this function with the parameter `$name`. See:


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

As you can see, we reduced our code and worked with even more efficient hooks. With triggers we can include functions in the code more intelligently without relying on functions equivalent to the action we are creating (since actions do not allow additional parameters outside the `Hook::run()` executation point).

It is very important to understand that a hook `Hook::run('new_ticket', $ticket)` is sending the `$ticket` parameter and this parameter will be received to any action as `Hook::action('new_ticket.notification', 'notification');`,  however it is not received by a `Hook::dispatch('new_ticket.notification', 'notification', $ticket);` which needs to include `$ticket` while is being registered.

> Actions must be used when they need to inherit `Hook::run()` execution parameters, while dispatchers must be used when we want to include a function with its own parameters inside the hook.

It is also possible to remove dispatchers before execution with the method `Hook::removeDispatcher()`.

Check below a practical example of using dispatchers:

```php
function name ( $message ) { echo sprintf("Your name: %s\n", $message); }
function prog ( $message ) { echo sprintf("Progamming Language: %s\n", $message); }

Hook::dispatch('sentences.name::1', 'name', 'Alpha');
Hook::dispatch('sentences.prog::1', 'prog', 'JS');

Hook::run('sentences', 'Peace and Love');
```

Full examples are available [here](samples/dispatchers.php).

## Changelog

See the [CHANGELOG](CHANGELOG.md) file for information about all code changes.

## Testing the code

This library uses the [PHPUnit](https://phpunit.de/). We carry out tests of all the main classes of this application.

```
vendor/bin/phpunit
```

## Contributions

See the file [CONTRIBUTING](CONTRIBUTING.md) for information before submitting your contribution.

## Security

If you discover any issues related to security, please send an email to [dev@piggly.com.br](mailto:dev@piggly.com.br) instead of using Github's issue tracker.

## Credits

- [Caique Araujo](https://github.com/caiquearaujo)
- [Todos os colaboradores](../../contributors)

## Support the project

**Piggly Studio** is an agency located in Rio de Janeiro, Brazil. If you like this library and want to support this job, be free to donate any value to BTC wallet `3DNssbspq7dURaVQH6yBoYwW3PhsNs8dnK` ❤.

## License

MIT License (MIT). See [LICENSE](LICENSE).