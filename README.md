WScore.DiContainer
==================

A simple Dependency Injection Container. 


Usage
-----

###Creating an instance

Use instance scripts

```php
$container = include( 'WScore.DiContainer/scripts/instance.php' );
```

###Setting and retrieving values.

Set and retrieve a value.

```php
$container->set( 'some-id', 'a value' );
$value = $container->get( 'some-id' ); // gets a string: "a value".
```

Set a service object.

```php
$container->set( 'service-this', '\name\space\className' );
$object = $container->get( 'service-this' ); // it's \name\space\className class.
```

Or, simply specify a class name to get an object.

```php
$object = $container->get( '\name\space\className2' );
```

###Getting another class

Set another class for a given class.

```php
$container->set( '\some\class', '\name\space\className' );
$object = $container->get( '\some\class' ); // it's \name\space\className class.
```

Auto-Wiring/Discovery
---------------------

Supports simple auto-wiring or auto-discovery of dependencies using annotations in phpdocs.
The supported tags are: `@Inject`, `@param`, and `@var`.

The @Singleton annotation is also supported.

Sample PHP class code:

```php
/**
 * @Singleton
 */
class Sample {
    /**
     * @Inject
     * @var /class/class
     */
    private $property;

    /**
     * @Inject
     * @param /some/class1 $var
     * @param /some/class2 $var2
     */
    function __construct( $var, $var2 ) {}

    /**
     * @Inject
     * @param /some/class1 $var3
     */
    function setVar3( $var3 ) {}
}
```


Overwriting DI Option
---------------------

The container can overwrite the dependency of any of the injection types: construct, setter, and property injections.
Specify the option at get,

```php
$container->option( 'Some\Class' )
    ->setConstructor( 'var', 'another\class' )
    ->setSetter( 'setVar3', 'setter\class', )
    ->setProperty( 'property', 'property\class' );
$object = $container->get( 'Some\Class );
```

