WScore.DiContainer
==================

A simple Dependency Injection Container. 


Usage
-----

###Creating an instance

Use instance scripts

    $container = include( 'WScore.DiContainer/scripts/instance.php' );


###Setting and retrieving values.

Set and retrieve a value.

    $container->set( 'some-id', 'a value' );
    $value = $container->get( 'some-id' );

Set a service object.

    $container->set( 'service-this', '\name\space\className' );
    $object = $container->get( 'service-this' );

Or, simply specify a class name to get an object.

    $object = $container->get( '\name\space\className2' );


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

supports dependency injection for construct, setter, and property injection. 

    $object = $container->get( 'Sample', array(
        'construct' => array( 'var'      => 'another\class', ),
        'setter'    => array( 'setVar3'  => 'setter\class', ),
        'property'  => array( 'property' => 'property\class', ),
    ) );



