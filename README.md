WScore.DiContainer
==================

A simple Dependency Injection Container. 


Examples
--------

get an object for a given class name. 

    $container = include( 'path/to/scripts/instance.php' );
    $object    = $container->get( '\name\space\className' );

set a service.

    $container->set( 'service-this', '\name\space\className' );
    $object    = $container->get( 'service-this' );

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

