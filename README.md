WScore.DiContainer
==================

A simple Dependency Injection Container. 


Usage
-----

###creating an instance

use instance scripts

    $container = include( 'WScore.DiContainer/scripts/instance.php' );


###setting and retrieving values.

set and retrieve a value.

    $container->set( 'some-id', 'a value' );
    $value = $container->get( 'some-id' );

set a service object.

    $container->set( 'service-this', '\name\space\className' );
    $object = $container->get( 'service-this' );

or, simply specify a class name to get an object. 

    $object = $container->get( '\name\space\className2' );


DI for Object Construction
--------------------------

supports dependency injection for construct, setter, and property injection. 

    $object = $container->get( '\name\space\className2', array(
        'construct' => array( 'argName'   => 'another\class', ),
        'setter'    => array( 'setMethod' => 'setter\class', ),
        'property'  => array( 'diProp'    => 'property\class', ),
    ) );

can set/overwrite options

    ```php
    #given a class like:
    class that {
        function __construct( $a ) {}
    }
    
    #inject classB into $a in the constructor.
    
    $container->setOption( 'service-that', array( 'a' => 'classB' ) );
    $object    = $container->get( 'service-that' );
    
    #or
    $object    = $container->get( 'service-that' array( 'a' => 'classC' ) );
    ```



Auto-wiring
-----------


```php
/**
 * @Inject
 * @param /some/class $var
 */
function name( $var ) {}
```

